/**
 * Global Function Exports
 * =======================
 * This file ensures that functions called from inline HTML onclick handlers
 * are available in the global scope (window object).
 *
 * Note: This is necessary because modular JS files don't automatically
 * expose functions to the global scope.
 */

// This file intentionally left empty - functions are already globally accessible
// because they're defined with 'function' keyword (not const/let) in non-module scripts.

console.log('✅ Globals initialized');
