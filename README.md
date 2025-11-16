# GovBrief Today - WordPress Custom Code

This repository contains custom theme and plugin code for GovBrief Today.

## Structure

```
wp-content/
  themes/
    generatepress-child/    # Custom child theme
  plugins/
    gov-brief-intensity-score/      # Intensity scoring plugin
    govbrief-publisher-simple/      # Publishing automation plugin
```

## Deployment

Changes pushed to the `main` branch automatically deploy to the live Pagely site via GitHub Actions.

## Local Development

Your working directory: `C:\Users\georg\Documents\govbrief-repo`

To work on files:
1. Edit files in the repo directory
2. Test on your Local by WP Flywheel site
3. Commit: `git add -A && git commit -m "Your message"`
4. Push: `git push origin main`
5. GitHub Actions deploys automatically

## Old Working Location

The old Git repo at `C:\Users\georg\Local Sites\govbrief-today-dev\app\public\wp-content\themes\generatepress-child` 
is now DEPRECATED. Work from `C:\Users\georg\Documents\govbrief-repo` instead.
