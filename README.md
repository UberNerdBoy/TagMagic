Tagmatic
========

Advanced (and highly experimental) automatic WordPress post tagging that uses custom filtering/algorithms to quantify the importance of individual words/phrases in your post content.

Right now it's just a prototype. It currently has 2 major caveats:

1. It considers the plural/singular forms of the same word to be separate
2. It doesn't pick up on multi-word "phrases" (eg. WordPress Template Hierarchy)


TODO:
=======

- [x] Filter out code blocks
- [x] Filter out HTML ASCII codes
- [x] Filter out HTML comments (normally associated with code blocks)
- [x] Filter out single quotes while preserving apostrophes
- [x] Filter out punctuation
- [x] Filter out multiple consecutive whitespaces
- [ ] Differentiate between plural/singular word forms and associate them as one word
- [ ] Examine preceeding and trailing words to associate multi-word "phrases"
- [ ] Incorporate into the WordPress environment and use actual post data
- [ ] Hook into publish post action and run filters before posting
