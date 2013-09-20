### WordPress CPT Hierarchical Taxonomy Permalinks ###

Ever wanted to have custom taxonomy permalinks that reflect parent/child relation?
Then this is for you. Still in development though.

These URL structures work:
- url.tld/photos/ loads archive-photo.php
- url.tld/photos/family/ loads taxonomy-album.php
- url.tld/photos/family/kids/ loads taxonomy-album.php
- url.tld/photos/family/kids/john loads single-photo.php
- url.tld/photos/page/2 loads page 2 with archive-photo.php

If you try this code, remember to flush your permalinks. Go to 'settings->permalinks' and it's done.

#### Known Bugs ####
- When a 'photo' is published without any taxonomy, the URL url.tld/photos/john does not work.
- The full URL url.tld/photos/family/kids/john/ has an unwanted trailing slash.

#### Contributions ####
Anyone is welcome to contribute. There are various ways you can contribute:

1. Raise an [Issue](https://github.com/keha76/WordPress-CPT-Hierarchical-Taxonomy-Permalinks/issues) on GitHub
2. Send a Pull Request with your bug fixes and/or new features
3. Provide feedback and suggestions on enhancements
