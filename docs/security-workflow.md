# Security Scanning Workflow

## Overview

This project uses both GitLab's built-in security scanners and custom security tools to detect vulnerabilities.

## Types of Security Scans

1. **Secret Detection** - Identifies hardcoded secrets in the codebase
2. **SAST (Static Application Security Testing)** - Analyzes code for security vulnerabilities
3. **Dependency Scanning** - Checks for vulnerabilities in dependencies

## When Security Scans Run

-   On every merge request
-   On commits to main and development branches
-   Weekly scheduled scans

## Handling Security Findings

1. Review the Security dashboard in GitLab
2. Address critical and high findings before merging code
3. Document false positives in the allowlist files

## Custom Configuration Files

-   `.gitlab/secret_detection_patterns.yml` - Custom patterns for secret detection
-   `.gitlab/secret_detection_allowlist.yml` - Patterns to exclude from secret detection
