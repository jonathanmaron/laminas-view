# laminas-view

[![Build Status](https://travis-ci.org/laminas/laminas-view.svg?branch=master)](https://travis-ci.org/laminas/laminas-view)
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-view/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-view?branch=master)

laminas-view provides the “View” layer of the Laminas MVC system. It is a
multi-tiered system allowing a variety of mechanisms for extension,
substitution, and more.

## Installation

Run the following to install this library:

```bash
$ composer require laminas/laminas-view
```

## Documentation

Browse the documentation online at https://docs.laminas.dev/laminas-view/

## Support

* [Issues](https://github.com/laminas/laminas-view/issues/)
* [Chat](https://laminas.dev/chat/)
* [Forum](https://discourse.laminas.dev/)

## Forked

On January 02, 2020, Laminas\View was forked.

To vastly improve performance in a very large Navigation tree, the `accept()` call now always returns `true` (li 315):

    public function accept(AbstractPage $page, $recursive = true)

    vendor/zendframework/zend-view/src/Helper/Navigation/AbstractHelper.php

This reduced the lookup time in the Navigation tree from:

    0.51467514038086 s, 3.1678 s
    
to:

    0.049877882003784 s, 0.5161 s

The first number is the time to for *one call* to the Navigation component, the second the *page execution* time (page contained several calls to Navigation component).

This only works, since we are not using the Visible flag nor the ACL functionality.
