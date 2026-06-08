<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Lawang Sewu Dashboard</title>
<!-- Fonts & Icons -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Theme Config -->
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "on-secondary-container": "#aeb9d0",
                "primary": "#adc6ff",
                "outline": "#8c909f",
                "on-primary-container": "#00285d",
                "tertiary-fixed": "#ffdcc6",
                "on-secondary-fixed-variant": "#3c475a",
                "surface-container-low": "#131b2e",
                "primary-fixed": "#d8e2ff",
                "secondary": "#bcc7de",
                "surface": "#0b1326",
                "tertiary-fixed-dim": "#ffb786",
                "on-surface": "#dae2fd",
                "on-secondary-fixed": "#111c2d",
                "secondary-fixed": "#d8e3fb",
                "error": "#ffb4ab",
                "inverse-on-surface": "#283044",
                "inverse-surface": "#dae2fd",
                "inverse-primary": "#005ac2",
                "surface-dim": "#0b1326",
                "surface-container": "#171f33",
                "surface-container-lowest": "#060e20",
                "on-tertiary-container": "#461f00",
                "surface-container-highest": "#2d3449",
                "primary-fixed-dim": "#adc6ff",
                "on-primary-fixed": "#001a42",
                "on-error": "#690005",
                "error-container": "#93000a",
                "surface-bright": "#31394d",
                "surface-variant": "#2d3449",
                "surface-container-high": "#222a3d",
                "on-tertiary": "#502400",
                "on-surface-variant": "#c2c6d6",
                "on-primary-fixed-variant": "#004395",
                "on-tertiary-fixed": "#311400",
                "primary-container": "#4d8eff",
                "on-error-container": "#ffdad6",
                "tertiary-container": "#df7412",
                "on-background": "#dae2fd",
                "surface-tint": "#adc6ff",
                "tertiary": "#ffb786",
                "secondary-fixed-dim": "#bcc7de",
                "background": "#0b1326",
                "on-tertiary-fixed-variant": "#723600",
                "secondary-container": "#3e495d",
                "on-secondary": "#263143",
                "on-primary": "#002e6a",
                "outline-variant": "#424754"
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            spacing: {
                "sm": "0.5rem",
                "xs": "0.25rem",
                "base": "4px",
                "2xl": "3rem",
                "md": "1rem",
                "lg": "1.5rem",
                "xl": "2rem",
                "margin": "32px",
                "gutter": "24px"
            },
            fontFamily: {
                "body-lg": ["Inter"],
                "body-md": ["Inter"],
                "display-lg": ["Inter"],
                "title-lg": ["Inter"],
                "headline-md": ["Inter"],
                "headline-lg": ["Inter"],
                "headline-lg-mobile": ["Inter"],
                "label-md": ["Inter"]
            },
            fontSize: {
                "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                "title-lg": ["20px", { "lineHeight": "28px", "fontWeight": "500" }],
                "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                "headline-lg-mobile": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }]
            }
        }
    }
}
</script>
<link rel="stylesheet" href="assets/css/custom.css">
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
