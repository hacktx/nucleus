# How to contribute
This document will outline how to contribute to the further development of Nucleus. While originally designed for use at HackTX, we are working towards a generic shell for other hackathons to build off of, so we appreciate any contributions. We have a few guidelines that we need contributors from the community to follow in order to keep things running smoothly.

## Getting Started

### Setting up on your own environment
After setting up the enviornment for development as outlined in the [wiki](https://github.com/hacktx/nucleus/wiki), fork then clone the repo replacing `your_username` with your actual GitHub username
```bash
git clone git@github.com:<your_username>/nucleus.git
```

### Using Vagrant
Since Nucleus has an involved enviornment setup, we've configured a Vagrant setup for easy development. You can find instructions for setup [here](https://github.com/hacktx/nucleus-vagrant).

## Formatting
We afform to the Hack formatting conventions. You can automatically format your file using `hh_format -i <file>`. 

## Making Changes

1. Make your commits in logical units. There should not be tiny changes in all commits, nor should all your work be condensed in one commit.
1. Make sure you continue to follow the whitespace and formatting conventions. Use `git diff --check` to verify before committing

## Submitting Changes
Once you have pushed all changes to your fork, you can submit a pull request to the HackTX repo. A member from the HackTX web team will review your changes. If it all looks good, we'll merge it, otherwise we'll make suggestions how to fix it.
