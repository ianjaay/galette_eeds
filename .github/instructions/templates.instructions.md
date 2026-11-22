---
applyTo: "galette/templates/**/*.twig"
---

Follow existing Twig and Fomantic/Semantic UI patterns. Escape ordinary values; only use `|raw` for content sanitized at storage using the project's approved `Html::clean()` flow. Preserve localization, accessibility, responsive behavior, and permission-aware visibility. Run the existing TwigCS check for material template changes.
