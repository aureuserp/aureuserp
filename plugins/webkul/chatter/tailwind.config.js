const preset = require('../../../vendor/filament/filament/tailwind.config.preset')

// Keep Filament's preset for consistent theming with Filament panels.
// Define content globs explicitly to satisfy CSS tooling and enable JIT extraction.
module.exports = {
    presets: [preset],
    content: [
        './resources/views/**/*.blade.php',
        './src/**/*.php',
    ],
}
