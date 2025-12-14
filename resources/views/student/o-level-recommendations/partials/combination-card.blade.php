<div class="combination-card {{ $matchType }}">
    <div class="card-header">
        <div class="combination-code">{{ $combo['code'] }}</div>
        <div class="combination-category">{{ $combo['category'] }}</div>
    </div>
    
    <div class="card-body">
        <h4 class="combination-name">{{ $combo['name'] }}</h4>
        
        <!-- Match Status -->
        @if($combo['match_score'] > 0)
            <div class="match-status {{ $matchType }}">
                @if($matchType === 'excellent')
                    <i class="fas fa-star"></i> Excellent Match ({{ $combo['match_score'] }} points)
                @elseif($matchType === 'good')
                    <i class="fas fa-check"></i> Good Match ({{ $combo['match_score'] }} points)
                @elseif($matchType === 'possible')
                    <i class="fas fa-info-circle"></i> Possible Match ({{ $combo['match_score'] }} points)
                @else
                    <i class="fas fa-circle"></i> Available Option
                @endif
            </div>
        @endif

        <!-- Subjects Analysis -->
        @if(count($combo['matched_subjects']) > 0)
            <div class="subjects-section">
                <strong><i class="fas fa-check-circle text-success"></i> Your Strengths:</strong>
                <div class="subject-list">
                    @foreach($combo['matched_subjects'] as $subject)
                        <span class="subject-tag matched">{{ $subject }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count($combo['missing_subjects']) > 0)
            <div class="subjects-section">
                <strong><i class="fas fa-exclamation-circle text-warning"></i> Need to Work On:</strong>
                <div class="subject-list">
                    @foreach($combo['missing_subjects'] as $subject)
                        <span class="subject-tag missing">{{ $subject }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Difficulty -->
        <div class="difficulty-badge {{ strtolower(str_replace(' ', '-', $combo['difficulty'])) }}">
            <i class="fas fa-signal"></i> {{ $combo['difficulty'] }} Difficulty
        </div>

        <!-- Career Paths -->
        <details class="careers-details">
            <summary><i class="fas fa-briefcase"></i> Career Opportunities ({{ count($combo['careers']) }})</summary>
            <ul class="careers-list">
                @foreach($combo['careers'] as $career)
                    <li>{{ $career }}</li>
                @endforeach
            </ul>
        </details>

        <!-- Universities -->
        <div class="universities-info">
            <i class="fas fa-university"></i>
            <small>{{ $combo['universities'] }}</small>
        </div>
    </div>
</div>

<style>
.combination-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.combination-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.combination-card.excellent {
    border-color: #28a745;
}

.combination-card.good {
    border-color: #17a2b8;
}

.combination-card.possible {
    border-color: #ffc107;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.combination-code {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.combination-category {
    font-size: 0.85rem;
    background: rgba(255,255,255,0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
}

.card-body {
    padding: 1.25rem;
}

.combination-name {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 1rem 0;
    line-height: 1.4;
}

.match-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.match-status.excellent {
    background: #d4edda;
    color: #155724;
}

.match-status.good {
    background: #d1ecf1;
    color: #0c5460;
}

.match-status.possible {
    background: #fff3cd;
    color: #856404;
}

.subjects-section {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.subjects-section strong {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.subject-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.subject-tag {
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    font-size: 0.85rem;
}

.subject-tag.matched {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.subject-tag.missing {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.difficulty-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.9rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 0.75rem 0;
}

.difficulty-badge.medium {
    background: #fff3cd;
    color: #856404;
}

.difficulty-badge.medium-high {
    background: #ffeeba;
    color: #856404;
}

.difficulty-badge.high {
    background: #f8d7da;
    color: #721c24;
}

.difficulty-badge.very-high {
    background: #f5c6cb;
    color: #721c24;
}

.careers-details {
    margin: 1rem 0;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    background: #fafbfc;
}

.careers-details summary {
    cursor: pointer;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.careers-details summary:hover {
    color: #667eea;
}

.careers-list {
    margin: 0.75rem 0 0 0;
    padding-left: 1.5rem;
}

.careers-list li {
    margin-bottom: 0.4rem;
    line-height: 1.5;
    color: #495057;
}

.universities-info {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.universities-info i {
    margin-top: 0.2rem;
    color: #667eea;
}

.text-success {
    color: #28a745 !important;
}

.text-warning {
    color: #ffc107 !important;
}
</style>
