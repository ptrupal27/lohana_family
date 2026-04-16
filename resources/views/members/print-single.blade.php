<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>સભ્ય પ્રિન્ટ - {{ $member->member_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            margin: 20px;
        }
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .print-header h1 {
            margin: 0;
            font-size: 22px;
        }
        .print-actions {
            display: flex;
            gap: 8px;
        }
        .btn {
            border: 1px solid #444;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            color: #111;
            font-size: 14px;
        }
        .hint {
            margin-bottom: 16px;
            padding: 10px 12px;
            background: #f6f6f6;
            border-left: 4px solid #800000;
            font-size: 14px;
        }
        .section {
            border: 1px solid #cfcfcf;
            border-radius: 8px;
            margin-bottom: 12px;
            padding: 12px;
        }
        .section h3 {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px;
            font-size: 13px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        @media print {
            .print-actions, .hint {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>સભ્યની વિગતો ({{ $member->member_no }})</h1>
        <div class="print-actions">
            <button type="button" class="btn" onclick="window.print()">પ્રિન્ટ કરો</button>
            <a href="{{ route('members.show', $member) }}" class="btn">પાછા જાઓ</a>
        </div>
    </div>

    <div class="hint">
        આ પેજ પ્રિન્ટ કરવા માટે <strong>પ્રિન્ટ કરો</strong> બટન દબાવો અથવા <strong>Ctrl + P</strong> દબાવો.
    </div>

    <div class="section">
        <h3>મુખ્ય સભ્યની વિગતો</h3>
        <div class="grid">
            <div><strong>સભ્ય નં.:</strong> {{ $member->member_no }}</div>
            <div><strong>નામ:</strong> {{ $member->full_name }}</div>
            <div><strong>મોબાઇલ નંબર:</strong> {{ $member->mobile }}</div>
            <div><strong>ઇમેઇલ:</strong> {{ $member->email ?? '-' }}</div>
            <div><strong>લિંગ:</strong> {{ $member->gender }}</div>
            <div><strong>જન્મ તારીખ:</strong> {{ \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') }}</div>
            <div><strong>વ્યવસાય:</strong> {{ $member->occupation ?? '-' }}</div>
            <div><strong>વતન:</strong> {{ $member->hometown ?? '-' }}</div>
            <div><strong>સરનામું:</strong> {{ $member->address }}</div>
            <div><strong>શહેર / ગામ:</strong> {{ $member->city_village }}</div>
        </div>
    </div>

    <div class="section">
        <h3>પરિવારના સભ્યો</h3>
        <table>
            <thead>
                <tr>
                    <th>સભ્ય નં.</th>
                    <th>નામ</th>
                    <th>સંબંધ</th>
                    <th>જન્મ તારીખ</th>
                    <th>મોબાઇલ નંબર</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->children as $familyMember)
                    <tr>
                        <td>{{ $familyMember->member_no }}</td>
                        <td>{{ $familyMember->first_name }} {{ $familyMember->last_name }}</td>
                        <td>{{ $familyMember->relation ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($familyMember->date_of_birth)->format('d/m/Y') }}</td>
                        <td>{{ $familyMember->mobile ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">પરિવારના સભ્યો ઉપલબ્ધ નથી.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
