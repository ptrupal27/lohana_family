function createFeedbackElement(form) {
    let feedback = form.querySelector("[data-api-feedback]");

    if (!feedback) {
        feedback = document.createElement("div");
        feedback.className =
            "alert alert-danger d-none animate__animated animate__shakeX";
        feedback.setAttribute("data-api-feedback", "true");
        form.prepend(feedback);
    }

    return feedback;
}

function clearValidationErrors(form) {
    const feedback = createFeedbackElement(form);
    feedback.classList.add("d-none");
    feedback.innerHTML = "";

    form.querySelectorAll(".is-invalid").forEach((el) => {
        el.classList.remove("is-invalid");
    });
    form.querySelectorAll(".invalid-feedback").forEach((el) => {
        el.remove();
    });
    form.querySelectorAll(".has-validation").forEach((el) => {
        el.classList.remove("has-validation");
    });
    form.querySelectorAll("label.text-danger").forEach((el) => {
        el.classList.remove("text-danger");
    });
}

function setFormSubmittingState(form, isSubmitting) {
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = isSubmitting;
    });
}

function renderValidationErrors(form, errors) {
    clearValidationErrors(form);

    const keys = Object.keys(errors ?? {});
    if (!keys.length) {
        return;
    }

    const feedback = createFeedbackElement(form);
    let errorFound = false;
    let firstErrorTabId = null;

    keys.forEach((key) => {
        const messages = errors[key];
        // Convert Laravel dot notation (family.0.first_name) to HTML name attribute (family[0][first_name])
        const inputName = key.includes(".")
            ? key
                  .split(".")
                  .map((part, index) => (index === 0 ? part : `[${part}]`))
                  .join("")
            : key;

        // Try to find by direct name or array format
        let input =
            form.querySelector(`[name="${inputName}"]`) ||
            form.querySelector(`[name="${key}"]`);

        // Fallback: try to find by ID if the key matches
        if (!input) {
            input =
                form.querySelector(`#${key}`) ||
                form.querySelector(`#${key.replace(/\./g, "_")}`);
        }

        if (input) {
            input.classList.add("is-invalid");

            // Color the label red
            const label = input.id
                ? form.querySelector(`label[for="${input.id}"]`)
                : input.previousElementSibling;
            if (label && label.tagName === "LABEL") {
                label.classList.add("text-danger");
            }

            const errorDiv = document.createElement("div");
            errorDiv.className =
                "text-danger fw-bold small mt-1 animate__animated animate__fadeIn";
            errorDiv.innerText = messages[0];

            const group = input.closest(".input-group");
            if (group) {
                group.classList.add("has-validation");
                group.appendChild(errorDiv);
            } else {
                input.after(errorDiv);
            }

            errorFound = true;

            // Find which tab this input belongs to
            const tabPane = input.closest(".tab-pane");
            if (tabPane && !firstErrorTabId) {
                firstErrorTabId = tabPane.id;
            }
        }
    });

    if (errorFound) {
        // If the error is in a different tab, switch to it
        if (firstErrorTabId && window.bootstrap) {
            const tabTriggerEl = document.querySelector(
                `[data-bs-target="#${firstErrorTabId}"]`,
            );
            if (tabTriggerEl) {
                const tab = new window.bootstrap.Tab(tabTriggerEl);
                tab.show();
            }
        }

        // Scroll to the first invalid field
        const firstInvalid = form.querySelector(".is-invalid");
        if (firstInvalid) {
            firstInvalid.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    }
}

function resolveRedirectUrl(form, response) {
    if (form.dataset.redirectUrl) {
        return form.dataset.redirectUrl;
    }

    const memberNumber = response?.data?.data?.member_no;

    if (form.dataset.redirectTemplate && memberNumber) {
        return form.dataset.redirectTemplate.replace(
            "__MEMBER__",
            memberNumber,
        );
    }

    return null;
}

async function handleApiFormSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const apiUrl = form.dataset.apiUrl;
    const apiMethod = (
        form.dataset.apiMethod ||
        form.method ||
        "POST"
    ).toUpperCase();

    if (!apiUrl) {
        return;
    }

    const formData = new FormData(form);

    // Use FormData.set to avoid duplicate _method fields if it already exists from @method
    if (apiMethod !== "POST") {
        formData.set("_method", apiMethod);
    }

    setFormSubmittingState(form, true);
    renderValidationErrors(form, {});

    try {
        const response = await window.axios.post(apiUrl, formData);
        const redirectUrl = resolveRedirectUrl(form, response);

        if (form.dataset.reload === "true") {
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

        window.alert("Request failed. Please try again.");
    } finally {
        setFormSubmittingState(form, false);
    }
}

async function handleApiDeleteSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const apiUrl = form.dataset.apiUrl;
    const confirmMessage = form.dataset.confirm || "Are you sure?";
    const hasInlineConfirmation = form.hasAttribute("onsubmit");

    if (!apiUrl) {
        return;
    }

    if (!hasInlineConfirmation && !window.confirm(confirmMessage)) {
        return;
    }

    try {
        await window.axios.delete(apiUrl);

        if (form.dataset.reload === "true") {
            window.location.reload();

            return;
        }

        if (form.dataset.redirectUrl) {
            window.location.assign(form.dataset.redirectUrl);

            return;
        }

        form.closest("tr")?.remove();
    } catch (error) {
        window.alert("Delete failed. Please try again.");
    }
}

function formatDate(dateString) {
    if (!dateString) {
        return "-";
    }

    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    return new Intl.DateTimeFormat("en-GB").format(date);
}

function genderLabel(gender) {
    if (gender === "Male") {
        return "પુરુષ";
    }

    if (gender === "Female") {
        return "સ્ત્રી";
    }

    if (gender === "Other") {
        return "અન્ય";
    }

    return gender || "-";
}

function fillMemberFields(container, member) {
    container.querySelectorAll("[data-member-field]").forEach((element) => {
        const field = element.dataset.memberField;

        if (field === "gender") {
            element.textContent = genderLabel(member.gender);

            return;
        }

        if (field === "date_of_birth") {
            element.textContent = formatDate(member.date_of_birth);

            return;
        }

        element.textContent = member[field] || "-";
    });

    const photoWrapper = container.querySelector("[data-member-photo-wrapper]");

    if (!photoWrapper) {
        return;
    }

    if (member.photo_url) {
        photoWrapper.innerHTML = `
            <div class="rounded-circle mx-auto mb-3 border p-1 shadow-sm overflow-hidden" style="width: 152px; height: 152px;">
                <img src="${member.photo_url}" alt="${member.full_name}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
            </div>
        `;

        return;
    }

    photoWrapper.innerHTML = `
        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
            <i class="bi bi-person" style="font-size: 5rem;"></i>
        </div>
    `;
}

export function bindApiForms(scope = document) {
    scope.querySelectorAll("form[data-api-form]").forEach((form) => {
        form.removeEventListener("submit", handleApiFormSubmit);
        form.addEventListener("submit", handleApiFormSubmit);
    });
}

export function bindApiDeleteForms(scope = document) {
    scope.querySelectorAll("form").forEach((form) => {
        if (form.dataset.apiDeleteForm !== undefined) {
            return;
        }

        const methodField = form.querySelector(
            'input[name="_method"][value="DELETE"]',
        );

        if (!methodField) {
            return;
        }

        const action = form.getAttribute("action");

        if (!action) {
            return;
        }

        try {
            const url = new URL(action, window.location.origin);
            const path = url.pathname.replace(/^\/+/, "");
            const isMemberDelete = /^members\/[^/]+$/.test(path);
            const isFamilyDelete =
                /^members\/[^/]+\/family-members\/[^/]+$/.test(path);

            if (!isMemberDelete && !isFamilyDelete) {
                return;
            }

            form.dataset.apiDeleteForm = "true";
            form.dataset.apiUrl = `${url.origin}/api/${path}`;
            form.dataset.reload = "true";
        } catch (error) {
            console.error("Unable to infer API delete URL.", error);
        }
    });

    scope.querySelectorAll("form[data-api-delete-form]").forEach((form) => {
        form.removeEventListener("submit", handleApiDeleteSubmit);
        form.addEventListener("submit", handleApiDeleteSubmit);
    });
}

function renderFamilyMembers(container, member) {
    const rowsContainer = container.querySelector("[data-member-family-rows]");

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

    rowsContainer.innerHTML = familyMembers
        .map(
            (familyMember) => `
        <tr>
            <td><span class="badge bg-soft-maroon text-maroon border small">${familyMember.member_no}</span></td>
            <td class="fw-bold">${familyMember.first_name} ${familyMember.last_name}</td>
            <td><span class="badge bg-light text-dark border">${familyMember.relation ?? "-"}</span></td>
            <td>${formatDate(familyMember.date_of_birth)}</td>
            <td>${familyMember.mobile ?? "-"}</td>
            <td class="text-end">
                <div class="btn-group">
                    <a href="${container.dataset.familyEditTemplate.replace("__FAMILY__", familyMember.member_no)}" class="btn btn-sm btn-outline-primary border-0">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form
                        class="d-inline"
                        data-api-delete-form
                        data-api-url="${container.dataset.familyDeleteTemplate.replace("__FAMILY__", familyMember.member_no)}"
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
    `,
        )
        .join("");

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
        console.error("Failed to load member details from API.", error);
    }
}
