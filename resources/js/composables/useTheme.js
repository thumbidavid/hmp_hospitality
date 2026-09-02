// resources/js/Composables/useTheme.js

import { inject } from 'vue';

// The key used here *must* match the string used in your ThemeProvider's provide()
const themeKey = 'theme';

export function useTheme() {
    // Inject the context provided by ThemeProvider.vue
    const context = inject(themeKey);

    if (!context) {
        // This is a helpful error for developers if they forget to wrap a component
        throw new Error(`useTheme must be used within a component wrapped by ThemeProvider (via the '${themeKey}' key).`);
    }

    // Returns the provided object: { isDarkMode, toggleTheme }
    return context;
}
