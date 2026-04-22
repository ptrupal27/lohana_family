<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>લેબલ પ્રિન્ટ</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}" type="image/jpeg">
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 20px;
            gap: 15px;
        }
        .label-card {
            background: white;
            border: 2px solid #000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 12px;
            box-sizing: border-box;
            position: relative;
            page-break-inside: avoid;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 5px;
            border-bottom: 2.5px solid #000;
            margin-bottom: 10px;
        }
        .member-no {
            font-size: 1rem;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
        }
        .family-no {
            font-size: 1rem;
            font-weight: 600;
            color: #000;
        }
        .body-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .name {
            font-size: 1.4rem;
            font-weight: 800;
            color: #000;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .info-row {
            font-size: 1rem;
            color: #000;
            line-height: 1.4;
            display: flex;
            gap: 5px;
        }
        .field-label {
            font-weight: 700;
            white-space: nowrap;
        }
        .field-value {
            font-weight: 400;
        }
        
        @media print {
            body {
                background-color: white;
            }
            .container {
                padding: 0;
                gap: 0;
            }
            .label-card {
                box-shadow: none;
                float: left;
                border: 2px solid #000; /* Ensure thick border prints */
            }
            .no-print {
                display: none;
            }
        }

        /* Controls styling */
        .controls {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1000;
            width: 220px;
            border: 1px solid #eee;
        }
        .btn-print {
            background: #000;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .btn-print:hover {
            background: #333;
            transform: translateY(-2px);
        }
        .btn-back {
            color: #666;
            text-decoration: none;
            display: block;
            text-align: center;
            font-size: 0.9rem;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="no-print controls">
        <button class="btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM2 6a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6zm3 3h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1z"/>
            </svg>
            પ્રિન્ટ કરો
        </button>
        <a href="javascript:window.close()" class="btn-back">બંધ કરો</a>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; font-size: 0.75rem; color: #888; text-align: center;">
            સાઈઝ: <strong>{{ $width }}mm x {{ $height }}mm</strong>
        </div>
    </div>

    <div class="container">
        @php
            $columnLabels = [
                'address' => 'Add:',
                'mobile' => 'Mobile:',
                'alternate_mobile' => 'Alt Mobile:',
                'city_village' => 'ગામ / શહેર:',
                'occupation' => 'વ્યવસાય:',
                'hometown' => 'વતન:',
            ];
            
            // Re-order members into a flat list for simple iteration if not grouped
            $allMembers = $groupByFamily ? $members : [$members];
        @endphp

        @if($groupByFamily)
            @foreach($members as $familyNo => $familyMembers)
                @foreach($familyMembers as $member)
                    <div class="label-card" style="width: {{ $width }}mm; height: {{ $height }}mm;">
                        <div class="header">
                            <span class="member-no">{{ $member->member_no }}</span>
                            <span class="family-no">પરિવાર: {{ $member->family_no }}</span>
                        </div>
                        <div class="body-content">
                            <div class="name">{{ $member->full_name }}</div>
                            
                            @foreach($selectedColumns as $col)
                                @if(!empty($member->$col))
                                    <div class="info-row">
                                        <span class="field-label">{{ $columnLabels[$col] ?? $col }}</span>
                                        <span class="field-value">{{ $member->$col }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach
        @else
            @foreach($members as $member)
                <div class="label-card" style="width: {{ $width }}mm; height: {{ $height }}mm;">
                    <div class="header">
                        <span class="member-no">{{ $member->member_no }}</span>
                        <span class="family-no">પરિવાર: {{ $member->family_no }}</span>
                    </div>
                    <div class="body-content">
                        <div class="name">{{ $member->full_name }}</div>
                        
                        @foreach($selectedColumns as $col)
                            @if(!empty($member->$col))
                                <div class="info-row">
                                    <span class="field-label">{{ $columnLabels[$col] ?? $col }}</span>
                                    <span class="field-value">{{ $member->$col }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>
