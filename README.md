# GovBrief Today - WordPress Custom Code

This repository contains custom theme and plugin code for GovBrief Today.

## Structure

```
generatepress-child/          # This folder = git repo root
  functions.php               # Shortcodes, utilities
  style.css                   # Child theme styles
  page-*.php                  # Custom page templates
  social-brief-template*.php  # Social image generation
  clear-cache-admin.php       # Cache management
```

Related plugins (not in this repo):
```
wp-content/plugins/
  gov-brief-intensity-score/      # Intensity scoring
  GBT-Shortcode-Finder/           # Shortcode audit tool
  govbrief-publisher-simple/      # Publishing automation
```

## Local Development

**Working directory / Git repo:**
`C:\Users\georg\Local Sites\govbrief-today-dev\app\public\wp-content\themes\generatepress-child`

**GitHub:** https://github.com/georgebounacos/govbrief

## Deploy Workflow

1. Edit and test in Local by Flywheel
2. Commit: `git add -A; git commit -m "Your message"; git push`
3. Copy changed files to OneDrive Desktop
4. Upload to Pagely via FileZilla

## Cache Clearing

Append `?clear_gb_cache=1` to any URL while logged in as admin.

## Related Docs

- Prompt library: `C:\Users\georg\OneDrive\Desktop\claude\prompts`
- Context files: `C:\Users\georg\OneDrive\Desktop\claude\context`
