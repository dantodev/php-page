[![Latest Stable Version](https://poser.pugx.org/dtkahl/php-page/v/stable)](https://packagist.org/packages/dtkahl/php-page)
[![License](https://poser.pugx.org/dtkahl/php-page/license)](https://packagist.org/packages/dtkahl/php-page)
[![Build Status](https://travis-ci.org/dtkahl/php-page.svg?branch=master)](https://travis-ci.org/dtkahl/php-page)

# PHP Page

PHP helper class for building HTML response.


## Dependencies

* `PHP >= 5.6.0`

## Usage

```php
$page = new \Dtkahl\Page\Page;
```


## Functionality

#### meta data

add meta data:

```php
$page->meta->set('title', 'foobar');
```

render meta data (ibnside your template):

```php
<?php echo $page->renderMeta() ?>
```

supported keys:
* title
* charset
* date
* copyright
* keywords
* viewport
* robots
* page-topic
* page-type
* og:type
* audience
* google-site-verification
* csrf-token
* twitter:site
* twitter:card
* local
* og:site_name
* description
* image
* url
* author
* publisher
* language
* (raw)

#### options
With options you can configure the page: 
* `title_pattern` : pattern for meta title. Example: `%s | foobar.com` 

```php
$page->options->set('title_pattern', `%s | foobar.com`);
```

#### asset managment (JS, CSS)

You can define which files should be loaded.

```php
$page->addJavascript('script'); // ".js" automatically added
```

```php
$page->addStylesheet('style'); // ".css" automatically added
```

... and render the includes in your template:

```php
<?php echo $page->renderJavascripts() ?>
```

```php
<?php echo $response->renderStylesheets() ?>
```

#### sections
With sections you have one more way to push informations to your view.

Example to pass simple information...

```php
$response->sections->set('foo', 'bar')
```

In your view you can retrieve the section:

```php
<?php echo $response->sections->get('foo') ?>
```


#### aliases

* `$page->meta($key)` does the same like `$page->meta->get($key)`
* `$page->meta($key, $value)` does the same like `$page->meta->set($key, $value)`
* `$page->option($key)` does the same like `$page->options->get($key)`
* `$page->option($key, $value)` does the same like `$page->options->set($key, $value)`
* `$page->section($key)` does the same like `$page->sections->get($key)`
* `$page->section($key, $value)` does the same like `$page->sections->set($key, $value)`
