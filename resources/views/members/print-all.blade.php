<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>બધા સભ્યો - પ્રિન્ટ</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}" type="image/jpeg">
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
        table {
            width: 100%;
            border-collapse: collapse;
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
        .text-center {
            text-align: center;
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
        <h1>બધા સભ્યોની યાદી</h1>
        <div class="print-actions">
            <button type="button" class="btn" onclick="window.print()">પ્રિન્ટ કરો</button>
            <a href="{{ route('members.index') }}" class="btn">પાછા જાઓ</a>
        </div>
    </div>

    <div class="hint">
        પ્રિન્ટ માટે ઉપરનો <strong>પ્રિન્ટ કરો</strong> બટન દબાવો અથવા કીબોર્ડમાં <strong>Ctrl + P</strong> દબાવો.
        @if(request()->filled('selected_members'))
            <br><strong>પસંદ કરેલ સભ્યોની પ્રિન્ટ:</strong> ફક્ત પસંદ કરેલી એન્ટ્રીઓ દર્શાવવામાં આવી છે.
        @endif
    </div>

    @php
        $columnLabels = [
            'member_no' => 'સભ્ય નં.',
            'full_name' => 'નામ',
            'family_no' => 'પરિવાર નં.',
            'mobile' => 'મોબાઇલ',
            'city_village' => 'શહેર / ગામ',
            'mother_name' => 'માતાનું નામ',
            'gender' => 'લિંગ',
            'occupation' => 'વ્યવસાય',
            'hometown' => 'વતન',
            'address' => 'સરનામું',
            'district' => 'જિલ્લો',
            'sub_district' => 'તાલુકો',
            'date_of_birth' => 'જન્મ તારીખ',
            'children_count' => 'પરિવારની સંખ્યા',
            'is_main' => 'સભ્ય પ્રકાર'
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th>ક્રમ</th>
                @foreach($selectedColumns as $column)
                    @if(isset($columnLabels[$column]))
                        <th>{{ $columnLabels[$column] }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach($selectedColumns as $column)
                        @if(isset($columnLabels[$column]))
                            <td class="{{ $column === 'children_count' ? 'text-center' : '' }}">
                                @if($column === 'full_name')
                                    {{ $member->full_name }}
                                @elseif($column === 'is_main')
                                    {{ $member->is_main ? 'મુખ્ય સભ્ય' : 'પરિવાર સભ્ય' }}
                                @elseif($column === 'date_of_birth')
                                    {{ \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') }}
                                @elseif($column === 'children_count')
                                    {{ $member->children_count }}
                                @else
                                    {{ $member->$column ?? '-' }}
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($selectedColumns) + 1 }}" class="text-center">કોઈ સભ્યો મળ્યા નથી.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
