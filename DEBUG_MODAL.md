# Modal Debug Guide

## Issue: Modal window doesn't open when clicking the user icon

### Debug Steps:

1. **Open Browser Console** (F12 → Console tab)

2. **Refresh the page** and check for these messages:
   ```
   Modal auth-modal initialized
   Modal profile-modal initialized
   ```
   
   **If you don't see these messages:**
   - Alpine.js is not loaded or initialized
   - Check Network tab for failed asset loading
   - Run `npm run build` to rebuild assets

3. **Click the user icon** and check for these messages:
   ```
   Auth modal clicked
   Event received: auth-modal Expected: auth-modal
   Modal auth-modal opened
   ```

4. **Common Issues and Solutions:**

### Issue 1: No console messages at all
**Cause:** Alpine.js not loaded

**Solutions:**
```bash
# Rebuild assets
npm run build

# Or run dev server
npm run dev

# Clear Laravel cache
php artisan cache:clear
php artisan view:clear
```

### Issue 2: "Alpine is not defined" error
**Cause:** Alpine.js script not loaded

**Solution:** Check `resources/views/share/layouts/base.blade.php` has Vite script tags

### Issue 3: Click event fires but modal doesn't open
**Cause:** Event listener mismatch

**Check:**
- Event name in button: `$dispatch('open-modal', 'auth-modal')`
- Event listener in modal: `x-on:open-modal.window`
- Modal name matches exactly

### Issue 4: Modal opens but is invisible
**Cause:** CSS z-index or display issues

**Check in DevTools Elements:**
- Modal has `z-50` class
- Modal `show` variable is `true` when clicked
- No overlaying elements blocking the modal

### Issue 5: Build assets not loading
**Cause:** Vite manifest missing

**Solution:**
```bash
# Build production assets
npm run build

# Verify build directory exists
ls -la public/build/
```

## Quick Test

Open browser console and run:

```javascript
// Test if Alpine is loaded
console.log('Alpine loaded:', typeof Alpine !== 'undefined');

// Test manual modal open
window.dispatchEvent(new CustomEvent('open-modal', { detail: 'auth-modal' }));
```

If the modal opens with this command, the issue is with the button click handler.

## Production Checklist

Before deploying:

1. ✅ Run `npm run build`
2. ✅ Verify `public/build/manifest.json` exists
3. ✅ Clear Laravel caches
4. ✅ Test modal in incognito mode
5. ✅ Remove console.log statements (optional)

## Expected Console Output (Working State)

```
Modal auth-modal initialized
Auth modal clicked
Event received: auth-modal Expected: auth-modal  
Modal auth-modal opened
```

## Files to Check

- `resources/views/components/modal.blade.php` - Base modal component
- `resources/views/components/auth-modal.blade.php` - Auth modal
- `resources/views/components/user-modal-login.blade.php` - User menu with button
- `resources/js/app.js` - Alpine.js initialization
- `resources/views/share/layouts/base.blade.php` - Scripts loading

