# Visual Design Guide - Student Portal

## Color System

### Primary Colors
```
Primary Blue:      #667eea
Secondary Purple:  #764ba2
Light Background:  #f5f7fa
White:            #ffffff
```

### Status Colors
```
✅ Success/Complete:  #10b981 (Green) | #43e97b (Bright Green)
⏳ Pending/In Progress: #d97706 (Orange) | #fbbf24 (Amber)
🔵 Submitted:        #3b82f6 (Blue) | #6366f1 (Indigo)
❌ Rejected/Needs:    #dc2626 (Red) | #ff6b6b (Coral)
ℹ️ Information:       #0284c7 (Sky Blue)
```

### Neutral Colors
```
Text Primary:   #333333
Text Secondary: #666666
Text Tertiary:  #999999
Border:         #e0e0e0
Background:     #f8f9fa
```

---

## Typography Scale

### Headers
- **H1**: 2.2rem (35px) - Page titles, welcome banner
- **H2**: 1.8rem (28px) - Section titles
- **H3**: 1.3rem (20px) - Card titles, group names
- **H4**: 1.1rem (18px) - Subsection titles
- **Body**: 1rem (16px) - Main content
- **Small**: 0.9rem (14px) - Meta information
- **Tiny**: 0.75rem (12px) - Labels, badges

### Font Weights
- **Light**: 300 (Not commonly used)
- **Regular**: 400 (Body text)
- **Medium**: 500 (Descriptions, meta)
- **Semibold**: 600 (Subheadings, labels)
- **Bold**: 700 (Headers, emphasize)

---

## Component Sizing

### Cards
```
Width:    320px - 400px (individual cards)
Padding:  20px - 30px
Height:   Auto (content-based)
Shadow:   0 4px 15px rgba(0,0,0,0.08)
Radius:   12px
Border:   2px solid #f0f0f0 (optional)
```

### Buttons
```
Primary:   #667eea background, white text
Secondary: #f0f0f0 background, #333 text
Hover:     Lifted (-5px), enhanced shadow
Height:    40px (touch-friendly minimum)
Padding:   10px 20px
Radius:    8px
```

### Icons
```
Section Headers:  1.3rem (20px)
Cards:           2rem (32px)
Progress Circles: 3rem (48px)
Small Badges:    0.7rem - 0.9rem
```

---

## Spacing System

### Padding
- **Extra Small**: 5px
- **Small**: 10px
- **Medium**: 15px
- **Large**: 20px
- **Extra Large**: 25px - 30px
- **Huge**: 40px

### Margins
- **Section Gap**: 30px - 40px
- **Card Gap**: 15px - 20px
- **Element Gap**: 10px - 15px
- **Top/Bottom**: 25px - 30px

---

## Grid Layouts

### Desktop (> 1024px)
```
Dashboard Stats: 4 columns (320px min)
Projects Grid:   3 columns (400px min)
Groups Grid:     3 columns (280px min)
Tables:          Full width with overflow
```

### Tablet (768px - 1024px)
```
Dashboard Stats: 2 columns
Projects Grid:   2 columns
Groups Grid:     2 columns
```

### Mobile (< 768px)
```
Dashboard Stats: 1 column
Projects Grid:   1 column
Groups Grid:     1 column
All buttons:     Full width
```

---

## Shadow Hierarchy

### Card Shadows
```
Resting:   0 2px 8px rgba(0,0,0,0.05)
Hover:     0 8px 20px rgba(0,0,0,0.12)
Elevated:  0 12px 30px rgba(0,0,0,0.15)
```

### Button Shadows
```
Default:   0 2px 4px rgba(0,0,0,0.1)
Hover:     0 4px 12px rgba(102,126,234,0.4)
Pressed:   0 1px 2px rgba(0,0,0,0.05)
```

---

## Animations & Transitions

### Timing
```
Quick:    0.2s (hover states, simple transitions)
Normal:   0.3s (card lifts, modal appears)
Slow:     0.5s (page transitions, complex animations)
```

### Easing
```
Ease:      cubic-bezier(0.25, 0.46, 0.45, 0.94)
Ease-in:   cubic-bezier(0.42, 0, 1, 1)
Ease-out:  cubic-bezier(0, 0, 0.58, 1)
```

### Transform Effects
```
Hover Lift:     translateY(-5px)
Button Hover:   translateY(-2px)
Slight Scale:   scale(1.02)
No Rotation:    (Keep cards flat)
```

---

## Badge System

### Status Badges
```
✅ Approved:
   Background: #d1fae5 (Mint)
   Color:      #065f46 (Dark green)
   Font:       600, uppercase

⏳ Pending:
   Background: #fef3c7 (Soft yellow)
   Color:      #d97706 (Amber)
   Font:       600, uppercase

🔵 Submitted:
   Background: #ddd6fe (Soft purple)
   Color:      #6366f1 (Indigo)
   Font:       600, uppercase

👑 Leader:
   Background: #fef3c7 (Soft yellow)
   Color:      #d97706 (Amber)
   Font:       600, uppercase
```

---

## Progress Indicators

### Visual Status Bars
```
Width:     100% of container
Height:    8px
Background: #e0e0e0
Fill:      linear-gradient(90deg, #667eea 0%, #764ba2 100%)
Radius:    4px
```

### Step Indicators (3 circles)
```
Diameter:    48px (3rem)
Completed:   Background #059669, white icon
Pending:     Background #e5e7eb, gray icon
Font Weight: 600
```

---

## Responsive Breakpoints

### Mobile First Approach
```
xs: 0px (default)
sm: 640px (small tablet)
md: 768px (tablet)
lg: 1024px (laptop)
xl: 1280px (desktop)
2xl: 1536px (large desktop)
```

### Media Query Template
```css
@media (max-width: 768px) {
    /* Tablet and mobile adjustments */
}

@media (max-width: 480px) {
    /* Mobile-only adjustments */
}
```

---

## Accessibility Considerations

### Color Contrast
```
Text on Background: Minimum 4.5:1 ratio (WCAG AA)
Large Text:         Minimum 3:1 ratio
Focus States:       Visible outline or border
```

### Touch Targets
```
Minimum Size:  48x48 pixels
Optimal:       56x56 pixels or larger
Spacing:       8px between touch targets
```

### Text
```
Minimum Font Size: 14px
Line Height:       1.5
Letter Spacing:    0.5px for caps
Max Width:         70-80 characters
```

---

## Hover & Focus States

### Cards
```
Border:    #667eea (from #e0e0e0)
Shadow:    Elevated to 0 8px 20px
Transform: translateY(-5px)
Duration:  0.3s
```

### Buttons
```
Opacity:   0.85 - 1.0
Shadow:    Increased by 2-3x
Transform: translateY(-2px)
Duration:  0.2s
```

### Links
```
Underline: Added or highlighted
Color:     #667eea (primary)
Visited:   #764ba2 (secondary)
Hover:     Darker shade
Focus:     Outline or underline
```

---

## Modal & Overlay

### Background Overlay
```
Background: rgba(0,0,0,0.5)
Duration:   0.3s fade-in
Z-index:    1000
```

### Modal Dialog
```
Width:      90% (mobile) | 600px (desktop)
Max-width:  600px
Padding:    30px
Border-radius: 12px
Shadow:     0 20px 25px rgba(0,0,0,0.15)
Z-index:    1001
```

---

## Form Elements

### Input Fields
```
Height:        40px
Padding:       10px 15px
Border:        2px solid #e0e0e0
Border-radius: 8px
Font-size:     16px (prevents zoom on iOS)
Focus Border:  #667eea
Focus Shadow:  0 0 0 3px rgba(102,126,234,0.1)
```

### Dropdowns
```
Height:        40px
Padding:       10px 15px
Same as Input Fields (consistency)
```

### Checkboxes & Radios
```
Size:          18x18 pixels
Checked Color: #667eea
Border:        2px solid #e0e0e0
Radius:        4px
```

---

## Dark Mode (Future Consideration)

### Dark Colors
```
Background:    #1a1a2e
Card:          #16213e
Text Primary:  #ffffff
Text Secondary: #b0b0b0
Accent:        #667eea (same)
```

---

## Performance Best Practices

### Images
```
Use modern formats: WebP with fallbacks
Lazy load: Images below fold
Optimize: Compress to <100KB
```

### CSS
```
Use CSS variables for consistency
Minimize specificity (avoid !important)
Use hardware acceleration (transform, will-change)
```

### JavaScript
```
Debounce scroll/resize events
Minimize DOM manipulation
Use CSS for animations when possible
```

---

## File Structure Reference

```
resources/views/
├── student/
│   ├── dashboard.blade.php (HOME)
│   ├── parent-progress.blade.php (PARENT PORTAL)
│   └── projects/
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── show.blade.php
│       └── groups/
│           ├── index.blade.php
│           ├── create.blade.php
│           └── show.blade.php

resources/css/
├── app.css (Global styles)
└── dashboard.css (Dashboard-specific styles)
```

---

## Testing Checklist

- [ ] All pages render correctly on mobile (320px+)
- [ ] All pages render correctly on tablet (768px+)
- [ ] All pages render correctly on desktop (1024px+)
- [ ] Hover effects work on desktop
- [ ] Touch targets are 48x48px minimum
- [ ] Color contrast meets WCAG AA standards
- [ ] All images are optimized
- [ ] No text overflows or cuts off
- [ ] Form inputs are accessible
- [ ] All buttons and links work
- [ ] Keyboard navigation works
- [ ] Page loads in <3 seconds
- [ ] No console errors or warnings
- [ ] Print styles look good (optional)

---

**Design System Version**: 1.0  
**Last Updated**: December 11, 2025  
**Status**: ✅ COMPLETE
