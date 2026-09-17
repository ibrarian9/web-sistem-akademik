---
trigger: always_on
description: Enforce anti-slop guidelines from antislop.md to eliminate AI-generated visual and copy clichés.
---

## antislop

This project adheres to the anti-slop guidelines defined in `antislop.md`.

Key Rules to Enforce:
- **No Em Dash in UI Text (R-02)**: Never use the em dash character (`—`) in titles, subtitles, badges, tables, or UI text. Use colon (`:`), pipe (`|`), comma (`,`), or parentheses `()` instead.
- **Mobile Responsiveness & Resilience (R-03)**: Ensure zero horizontal overflow, text that never breaks out of bounds, responsive wrapping, and touch-friendly controls (minimum 44px tap target).
- **No Redundant Eyebrow Badges (R-09)**: Never place an uppercase pill badge above an H1 title that merely repeats what the title already says.
- **No Dead Controls (R-26)**: Never use dead anchors (`href="#"`) or non-functional interactive elements. Every element must have a real action or state.
- **Contrast & Legibility (R-25)**: Ensure all text complies with WCAG AA standards (minimum 4.5:1 for normal text).
- **Real Data & Intentionality (R-17, R-31, R-36)**: Display authentic system metrics and write with clear intent without AI marketing buzzwords.
