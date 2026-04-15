function createFeedbackElement(form) {
    let feedback = form.querySelector('[data-api-feedback]');

    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'alert alert-danger d-none';
        feedback.setAttribute('data-api-feedback', 'true');
        form.prepend(feedback);
    }

    return feedback;
}

function setFormSubmittingState(form, isSubmitting) {
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = isSubmitting;
    });
}

function renderValidationErrors(form, errors) {
    const feedback = createFeedbackElement(form);
    const messages = Object.values(errors ?? {}).flat();

    if (!messages.length) {
        feedback.classList.add('d-none');
        feedback.innerHTML = '';

        return;
    }

    feedback.innerHTML = `
        <h6 class="fw-bold mb-2">Please fix the following errors:</h6>
        <ul class="mb-0">${messages.map((message) => `<li>${message}</li>`).join('')}</ul>
    `;
    feedback.classList.remove('d-none');
    feedback.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resolveRedirectUrl(form, response) {
    if (form.dataset.redirectUrl) {
        return form.dataset.redirectUrl;
    }

    const memberNumber = response?.data?.data?.member_no;

    if (form.dataset.redirectTemplate && memberNumber) {
        return form.dataset.redirectTemplate.replace('__MEMBER__', memberNumber);
    }

    return null;
}

async function handleApiFormSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const apiUrl = form.dataset.apiUrl;
    const apiMethod = (form.dataset.apiMethod || form.method || 'POST').toUpperCase();

    if (!apiUrl) {
        return;
    }

    const formData = new FormData(form);

    if (apiMethod !== 'POST') {
        formData.append('_method', apiMethod);
    }

    setFormSubmittingState(form, true);
    renderValidationErrors(form, {});

    try {
        const response = await window.axios.post(apiUrl, formData);
        const redirectUrl = resolveRedirectUrl(form, response);

        if (form.dataset.reload === 'true') {
            window.location.reload();

            return;
        }

        if (redirectUrl) {
            window.location.assign(redirectUrl);

            return;
        }

        form.reset();
    } catch (error) {
        if (error.response?.status === 422) {
            renderValidationErrors(form, error.response.data.errors);

            return;
        }

        window.alert('Request failed. Please try again.');
    } finally {
        setFormSubmittingState(form, false);
    }
}

async function handleApiDeleteSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const apiUrl = form.dataset.apiUrl;
    const confirmMessage = form.dataset.confirm || 'Are you sure?';
    const hasInlineConfirmation = form.hasAttribute('onsubmit');

    if (!apiUrl) {
        return;
    }

    if (!hasInlineConfirmation && !window.confirm(confirmMessage)) {
        return;
    }

    try {
        await window.axios.delete(apiUrl);

        if (form.dataset.reload === 'true') {
            window.location.reload();

            return;
        }

        if (form.dataset.redirectUrl) {
            window.location.assign(form.dataset.redirectUrl);

            return;
        }

        form.closest('tr')?.remove();
    } catch (error) {
        window.alert('Delete failed. Please try again.');
    }
}

function formatDate(dateString) {
    if (!dateString) {
        return '-';
    }

    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    return new Intl.DateTimeFormat('en-GB').format(date);
}

function genderLabel(gender) {
    if (gender === 'Male') {
        return 'પુરુષ';
    }

    if (gender === 'Female') {
        return 'સ્ત્રી';
    }

    if (gender === 'Other') {
        return 'અન્ય';
    }

    return gender || '-';
}

function fillMemberFields(container, member) {
    container.querySelectorAll('[data-member-field]').forEach((element) => {
        const field = element.dataset.memberField;

        if (field === 'gender') {
            element.textContent = genderLabel(member.gender);

            return;
        }

        if (field === 'date_of_birth') {
            element.textContent = formatDate(member.date_of_birth);

            return;
        }

        element.textContent = member[field] || '-';
    });

    const photoWrapper = container.querySelector('[data-member-photo-wrapper]');

    if (!photoWrapper) {
        return;
    }

    if (member.photo_url) {
        photoWrapper.innerHTML = `<img src="${member.photo_url}" alt="${member.full_name}" class="rounded-circle mb-3 border p-1" width="150" height="150" style="object-fit: cover;">`;

        return;
    }

    photoWrapper.innerHTML = `
        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
            <i class="bi bi-person" style="font-size: 5rem;"></i>
        </div>
    `;
}

export function bindApiForms(scope = document) {
    scope.querySelectorAll('form[data-api-form]').forEach((form) => {
        form.removeEventListener('submit', handleApiFormSubmit);
        form.addEventListener('submit', handleApiFormSubmit);
    });
}

export function bindApiDeleteForms(scope = document) {
    scope.querySelectorAll('form').forEach((form) => {
        if (form.dataset.apiDeleteForm !== undefined) {
            return;
        }

        const methodField = form.querySelector('input[name="_method"][value="DELETE"]');

        if (!methodField) {
            return;
        }

        const action = form.getAttribute('action');

        if (!action) {
            return;
        }

        try {
            const url = new URL(action, window.location.origin);
            const path = url.pathname.replace(/^\/+/, '');
            const isMemberDelete = /^members\/[^/]+$/.test(path);
            const isFamilyDelete = /^members\/[^/]+\/family-members\/[^/]+$/.test(path);

            if (!isMemberDelete && !isFamilyDelete) {
                return;
            }

            form.dataset.apiDeleteForm = 'true';
            form.dataset.apiUrl = `${url.origin}/api/${path}`;
            form.dataset.reload = 'true';
        } catch (error) {
            console.error('Unable to infer API delete URL.', error);
        }
    });

    scope.querySelectorAll('form[data-api-delete-form]').forEach((form) => {
        form.removeEventListener('submit', handleApiDeleteSubmit);
        form.addEventListener('submit', handleApiDeleteSubmit);
    });
}

function renderFamilyMembers(container, member) {
    const rowsContainer = container.querySelector('[data-member-family-rows]');

    if (!rowsContainer) {
        return;
    }

    const familyMembers = member.family_members ?? [];

    if (!familyMembers.length) {
        rowsContainer.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">કોઈ પરિવારના સભ્યો નથી.</td>
            </tr>
        `;

        return;
    }

    rowsContainer.innerHTML = familyMembers.map((familyMember) => `
        <tr>
            <td><span class="badge bg-soft-maroon text-maroon border small">${familyMember.member_no}</span></td>
            <td class="fw-bold">${familyMember.first_name} ${familyMember.last_name}</td>
            <td><span class="badge bg-light text-dark border">${familyMember.relation ?? '-'}</span></td>
            <td>${formatDate(familyMember.date_of_birth)}</td>
            <td>${familyMember.mobile ?? '-'}</td>
            <td class="text-end">
                <div class="btn-group">
                    <a href="${container.dataset.familyEditTemplate.replace('__FAMILY__', familyMember.member_no)}" class="btn btn-sm btn-outline-primary border-0">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form
                        class="d-inline"
                        data-api-delete-form
                        data-api-url="${container.dataset.familyDeleteTemplate.replace('__FAMILY__', familyMember.member_no)}"
                        data-reload="true"
                        data-redirect-url="${container.dataset.reloadUrl}"
                        data-confirm="શું તમે ખરેખર આ સભ્યને કાઢી નાખવા માંગો છો?"
                    >
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    `).join('');

    bindApiDeleteForms(rowsContainer);
}

export async function hydrateMemberShowPage(container) {
    if (!container.dataset.apiShowUrl) {
        return;
    }

    try {
        const response = await window.axios.get(container.dataset.apiShowUrl);
        const member = response.data.data;

        fillMemberFields(container, member);
        renderFamilyMembers(container, member);
    } catch (error) {
        console.error('Failed to load member details from API.', error);
    }
}
