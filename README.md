[![Latest Stable Version](https://poser.pugx.org/dtkahl/page-response/v/stable)](https://packagist.org/packages/dtkahl/page-response)
[![License](https://poser.pugx.org/dtkahl/page-response/license)](https://packagist.org/packages/dtkahl/page-response)
[![Build Status](https://travis-ci.org/dtkahl/page-response.svg?branch=master)](https://travis-ci.org/dtkahl/page-response)

# PHP PageResponse

Extended ResponseObject for slim3.


## Dependencies

* `PHP >= 5.6.0`

## Usage

```php
$response = new PageResponse(
    new ViewRenderer(__DIR__.'/views/'), // ViewRender
    'layout.php' // master layout
);
```


## Functionality

### Master layout
The master layout should contain the main HTML structur of your application.
It can be defined on costruct or through `options` property:

```php
$response->options->set('master_view', 'example.php');
```

### Render data
Informations you store in render data, will be pushed directly as variable to the master view when you render the response.
You can pass render data to construct() and render() methods or through `redner_data` property:

```php
$response->redner_data->set('foo', 'bar');
```

### Meta data
Automatically generate meta for HTML head. You cann add as example a title through `meta` property:

```php
$response->redner_data->set('title', 'foobar');
```

**Info:** the render data key 'response' is reserved for response instance.

You have to place the following in your master layout HTML head:

```php
<?php echo $response->renderMeta() ?>
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

### Options
With options you can configure the response. 
* `master_view` : master view for response
* `title_pattern` : pattern for meta title. Example: `%s | foobar.com` 

#### asset managment (JS, CSS)
You cann define which files should be loaded.

```php
$response->addJavascript('script'); // ".js" automatically added
```

```php
$response->addStylesheet('style'); // ".css" automatically added
```

In the master view you have to include the following on your desired position:

```php
<?php echo $response->renderJavascripts() ?>
```

```php
<?php echo $response->renderStylesheets() ?>
```

#### sections
With sections you have one more way to push informations to the master view.

Example to pass simple information...

```php
$response->sections->set('foo', 'bar')
```

...or a subview...

```php
$response->sections->set('profile', $response->view('profile.php', ['id' => 4]))
```

In master layout you have to do the following:

```php
<?php echo $response->sections->get('foo') ?>
```
