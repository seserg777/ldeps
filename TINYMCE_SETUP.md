# TinyMCE Setup Instructions

## Getting a Free API Key

TinyMCE offers a **free tier** for basic usage. Follow these steps to get your API key:

1. **Sign up for free** at: https://www.tiny.cloud/auth/signup/

2. **Create a new account** (or log in if you already have one)

3. **Get your API key** from the dashboard at: https://www.tiny.cloud/my-account/dashboard/

4. **Copy your API key** - it will look something like: `abc123xyz456def789ghi012jkl345mno678`

## Free Tier Limits

The free tier includes:
- **Unlimited editor loads** for testing and development
- **1,000 editor loads per month** for production use
- All core plugins
- Community support

## Applying Your API Key

Replace `YOUR_API_KEY_HERE` with your actual API key in these files:

### 1. Manufacturer Forms
- `resources/views/admin/manufacturers/create.blade.php`
- `resources/views/admin/manufacturers/edit.blade.php`

**Find this line:**
```html
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY_HERE/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
```

**Replace with:**
```html
<script src="https://cdn.tiny.cloud/1/abc123xyz456def789ghi012jkl345mno678/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
```

### 2. Other Forms (if you add TinyMCE elsewhere)

Use the same pattern wherever you include TinyMCE.

## Alternative: Self-Hosted Version

If you prefer not to use the cloud version, you can download and self-host TinyMCE:

1. Download from: https://www.tiny.cloud/get-tiny/self-hosted/
2. Place files in `public/vendor/tinymce/`
3. Update script tag to: `<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>`

**Note:** Self-hosted version doesn't require an API key but needs manual updates.

## Current Configuration

TinyMCE is configured with:
- **Height:** 400px
- **Language:** Russian (ru)
- **Plugins:** advlist, autolink, lists, link, image, charmap, preview, anchor, searchreplace, visualblocks, code, fullscreen, insertdatetime, media, table, help, wordcount
- **Toolbar:** Basic formatting, alignment, lists, links, images, code view

## Support

- **Documentation:** https://www.tiny.cloud/docs/
- **Community Forum:** https://community.tiny.cloud/
- **GitHub:** https://github.com/tinymce/tinymce

