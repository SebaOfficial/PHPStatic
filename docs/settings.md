<style>
	@import url('/DocsStyling.css');
</style>

# Settings
Every project built with this framework should have a `phpstatic.json` file and it should look like this:
```json
{
    "pages": {
        "minify": false,
        "path": "@/src/pages/",
        "public_path": "@/src/public/",
        "template": "@/src/template.html",
        "distDir": "@/dist/"
    },
    "languages": {
        "path": "@/src/locales/",
        "default": "en-US",
        "redirectOnDefault": true
    },
    "server": {
        "address": "localhost",
        "port": null
    },
    "seo": {
        "always": false,
        "buildInPublic": false,
        "url": null,
        "sitemap": {
            "structure": "sitemap-#{{lang}}.xml",
            "pages": {
                
            }
        },
        "robots": {
            "disallow": [
                
            ]
        }
    },
    "public_path": "@/src/public/",
    "global_variables": "@/src/variables.php"
}
```
*These are the default values*

## Let's break it down
*Note: `@` indicates the root directory of the project.*

* **`pages`**:
    * **`minify`** (`boolean`): Whether to minify the html contents. If set to true, the pages will be minified;
    * **`path`** (`string`): The path to the directory where your project's pages are located;
    * **`public_path`** (`string`): The path to the directory where all the public files are located;
    * **`template`** (`string`): The path to the template file that will be used for rendering pages;
    * **`distDir`** (`string`): The directory where the generated (compiled) pages will be stored;
* **`languages`**:
    * **`path`** (`string`): The path to the directory where your project's language/locale files are located;
    * **`default`** (`string`): The default language/locale for your application;
    * **`redirectOnDefault`** (`boolean`): Wheter the user should be redirected to the default language if no language is specified;
* **`seo`**:
    * **`always`** (`bool`): Wheter `composer build` should always be `composer build-seo` (*see [cli](cli.html)*);
    * **`sitemap`**:
        * **`url`** (`string|null`): The url specified in the sitemap, pass `null` to use http://{hostName}, where {hostName} is set by [`gethostname`](https://www.php.net/manual/en/function.gethostname.php);
        * **`buildInPublic`** (`bool`): Wheter the sitemap should be creatent in the `public/` directory or in the `dist/` directory;
        * **`structure`** (`string`): The structure of the sitemap, use `#{{lang}}` to specify the sitemap language;
        * **`pages`** (`object|null`):
            * **`pageName`** (`string`): Replace `pageName` with the page name used in the `pages/` directory (without the file extension), i.e `index` for `index.html`;
                * **`priority`** (`float`): Custom priority for the page, if not specified `0.5` is set;
                * **`changefreq`** (`string`): Change frequency of the page, omitted if not specidfied;
    * **`robots`**:
        * **`disallow`** (`array`): A list of disallowed directories and files, all the other files will be allowed;
* **`server`**:
    * **`adrress`** (`string`): The server's ip address (make it `0.0.0.0` to make the web server accessible to any interface;
    * **`port`** (`int|null`): The server's port, `null` to let the framework decide;
* **`public_path`** (`string`): The path to the directory where your project's public assets (e.g., images, stylesheets, JavaScript files) are stored.
* **`global_variables`** (`string`): The path to the global variables.


<div class="actions">
	<a href="index.html" title="Previous Page: Index">&lt;&lt; Index</a>
    <a href="variables.html" title="Next Page: Variables">Variables &gt;&gt;</a>
</div>