# Mergebot
Does other stuff too, but the name it was it is.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a338918b69e848e886e69da8537e7764)](https://app.codacy.com/app/pmclain/mergebot?utm_source=github.com&utm_medium=referral&utm_content=pmclain/mergebot&utm_campaign=Badge_Grade_Dashboard)
[![Build Status](https://travis-ci.org/pmclain/mergebot.svg?branch=master)](https://travis-ci.org/pmclain/mergebot)
[![Coverage Status](https://coveralls.io/repos/github/pmclain/mergebot/badge.svg?branch=master)](https://coveralls.io/github/pmclain/mergebot?branch=master)

## Setup
1. Copy `.env.dist` to `.env` required variables are:
   * `RABBITMQ_URL`
   * `GITHUB_USERNAME`
   * `GITHUB_ACCESS_TOKEN`
   * `GITHUB_WEBHOOK_SECRET`
2. `composer install`
3. `bin/console cache:clear --env=prod --no-debug`
4. `bin/console rabbitmq:setup-fabric`
4. Add cron to process queue items `* * * * * {{path_to_php}}/php {{install_path}}bin/console rabbitmq:consumer github_pr`

## Configure Repositories
Add `.mergebot.yml`([sample](.mergebot.yml.sample)) to repository root. The release body will contain a markdown list
containing the first line of all non-merge commit messages since the list release. The first release cannot be created
with this feature.

### Release Creation
Create releases by adding the text `ReleaseMe {{tagName}}` eg `ReleaseMe v1.0.0` to a PR body.
- **Yaml Path**: `pullRequest/closed/releaseMe`
- **Options**: `targetBranch` Only PRs merged into this branch will trigger a release.

### Merge
Merge PRs when conditions are met.
- **Yaml Path**: `pullRequest/opened/autoMerge/mergeCondition`
- **Conditions**
  - **whitelistedFiles/allowedFiles**: Contains properties representing relative file paths. Each property contains an
  array of file actions allowed eg `modified`, `added`

## License
MIT