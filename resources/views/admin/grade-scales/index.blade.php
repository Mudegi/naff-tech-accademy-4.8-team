@extends('layouts.dashboard')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 32px; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);">
        <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0 0 12px 0;">📊 Grading Scale Management</h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 0;">Configure custom grading scales for your school or use default Uganda standards</p>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- O-Level Section -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 32px; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px;">
            <h2 style="color: white; font-size: 24px; font-weight: 600; margin: 0;">O-Level Grading Scale (UCE)</h2>
        </div>
        
        <div style="padding: 24px;">
            @if($customOLevelScales->isNotEmpty())
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>⚠️ Using Custom Scale</strong> - Your school has a custom O-Level grading scale.
                    <form method="POST" action="{{ route('admin.grade-scales.destroy', 'O-Level') }}" style="display: inline; margin-left: 16px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;" onclick="return confirm('Reset to default O-Level scale?')">
                            🔄 Reset to Default
                        </button>
                    </form>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Grade</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Percentage Range</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customOLevelScales as $scale)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: 600; font-size: 18px; color: #667eea;">{{ $scale->grade }}</td>
                                <td style="padding: 12px;">{{ $scale->min_percentage }}% - {{ $scale->max_percentage }}%</td>
                                <td style="padding: 12px;">
                                    <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                        {{ $scale->points }} points
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    ℹ️ <strong>Using Default Scale</strong> - Standard Uganda UCE grading scale
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Grade</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Percentage Range</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaultOLevelScales as $scale)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: 600; font-size: 18px; color: #6c757d;">{{ $scale->grade }}</td>
                                <td style="padding: 12px;">{{ $scale->min_percentage }}% - {{ $scale->max_percentage }}%</td>
                                <td style="padding: 12px;">
                                    <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                        {{ $scale->points }} points
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.grade-scales.create') }}?level=O-Level" style="background: #667eea; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600;">
                        ✏️ Create Custom O-Level Scale
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- A-Level Section -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px;">
            <h2 style="color: white; font-size: 24px; font-weight: 600; margin: 0;">A-Level Grading Scale (UACE)</h2>
        </div>
        
        <div style="padding: 24px;">
            @if($customALevelScales->isNotEmpty())
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>⚠️ Using Custom Scale</strong> - Your school has a custom A-Level grading scale.
                    <form method="POST" action="{{ route('admin.grade-scales.destroy', 'A-Level') }}" style="display: inline; margin-left: 16px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;" onclick="return confirm('Reset to default A-Level scale?')">
                            🔄 Reset to Default
                        </button>
                    </form>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Grade</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Percentage Range</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customALevelScales as $scale)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: 600; font-size: 18px; color: #f5576c;">{{ $scale->grade }}</td>
                                <td style="padding: 12px;">{{ $scale->min_percentage }}% - {{ $scale->max_percentage }}%</td>
                                <td style="padding: 12px;">
                                    <span style="background: #f5576c; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                        {{ $scale->points }} points
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                    ℹ️ <strong>Using Default Scale</strong> - Standard Uganda UACE grading scale
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Grade</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Percentage Range</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-weight: 600;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaultALevelScales as $scale)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: 600; font-size: 18px; color: #6c757d;">{{ $scale->grade }}</td>
                                <td style="padding: 12px;">{{ $scale->min_percentage }}% - {{ $scale->max_percentage }}%</td>
                                <td style="padding: 12px;">
                                    <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                        {{ $scale->points }} points
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.grade-scales.create') }}?level=A-Level" style="background: #f5576c; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600;">
                        ✏️ Create Custom A-Level Scale
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
