<style>
	@import url('/DocsStyling.css');
</style>

# Variables
Variables are in the form `#{{variable.subvariable}}`.
There are 3 types of varibles:

## Local variables
Local varibales are stored in your locales directory.

### Example
Let's assume that you have an `index.html` file in the pages directory that looks like this:
```html
<h1>#{{cool.title}}</h1>
<h2>#{{description}}</h2>
```

And the files in your locales directory look like this:
```json
{
    "description": "Your description",
    "cool": {
        "title": "Your title"
    }
}
```


## Global variables
Global variables determine variables shared across all the pages.
```php
<?php

return [
    "#{{CustomVariable}}" => "Hello World!"
];

?>
```

## Framework variables
Framework variables are a special type of variables, here's a list of them:

* **`"#{{PAGE_CONTENTS}}`**: The contents of the page, must be in the `template.html` file;
* **`"#{{CURRENT_LANGUAGE}}`**: The current language of the page;
* **`#{{CURRENT_PAGE}}`**: The current page name (i.e. "pagename.html" => "pagename");
* **`"#{{CURRENT_PAGE_NO_INDEX}}`**: Same as `#{{CURRENT_PAGE}}` but "index" is removed.

<div class="actions">
    <a href="settings.html" title="Previous Page: Settings">&lt;&lt; Settings</a>
    <a href="examples.html" title="Next Page: Examples">Examples &gt;&gt;</a>
</div>