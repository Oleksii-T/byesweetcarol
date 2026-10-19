# RCClubGame

RCClubGame is a website about video games that has been created to provide useful information and help you find the best video games. Our team works to create the most objective guides and reviews from real experience. We cover video games on PC, mobile, and consoles.

Project is on post-production stage.

## Features
- Categories - reviews, guides, news, e.g
- Games
- Flexible content builder
- Views monitoring
- Admin panel

## Delete old posts

Preview how many posts were created before a date:

```sh
php artisan posts:delete-older-than 2026-01-01 --dry-run
```

Soft delete those posts:

```sh
php artisan posts:delete-older-than 2026-01-01
```

The cutoff is exclusive and uses `created_at` at midnight in the application timezone.
Posts on or after the date and posts already soft deleted are skipped. Matching posts
are processed in batches with the usual model deletion events and activity logging.

## Clean images from trashed posts

Preview images used exclusively by soft-deleted posts:

```sh
php artisan posts:clean-images --dry-run
```

Permanently delete those image files, attachment records, and attachment links:

```sh
php artisan posts:clean-images --batch-size=100
```

The command checks post attachments, content blocks, and inline image URLs. Images
shared with retained content or other owners (including authors, games, and pages)
are preserved, as are unrelated uploads and non-image attachments. Posts stay soft
deleted. Restoring a post after cleanup will not restore its deleted images.
Failed deletions are reported and return a nonzero exit status.
