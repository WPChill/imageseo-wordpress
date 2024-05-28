# ImageSEO WordPress

> Official WordPress plugin of ImageSEO : https://imageseo.io

---

Optimize images alternative texts (alt text or alt tag) and names with SEO friendly content. Optimize image html attributes on upload or bulk your library. Save time and improve your accessibility and SEO.

Beside alt tags, image SEO optimizer also generates Social Media preview cards for your posts using the Open Graph protocol (og tag).

## Requirements

-   PHP version 7.4 and later
-   ImageSEO API Key, starting at [free level](https://imageseo.io/register)

## Getting Started

If you're looking to contribute to imageSEO, welcome! We're glad you're here. Please ⭐️ this repository and fork it to begin local development.

Most of us are using [Local by Flywheel](https://localbyflywheel.com/) to develop on WordPress, which makes set up quick and easy. If you prefer [Docker](https://www.docker.com/), [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV), or another flavor of local development that's cool too!

## Prerequisites

-   [Node.js](https://nodejs.org/en/) as JavaScript engine.
-   [NPM](https://docs.npmjs.com/) npm command globally available in CLI.
-   [WP CLI](https://wp-cli.org) wp-cli command globally available (Local by Flywheel has this built-in).

## Local Development

To get started developing you will need to perform the following steps:

1. Create a new WordPress site using your favorite local development software.
2. `cd` into your local plugins directory: `/wp-content/plugins/`
3. Fork this repository from GitHub and then clone that into your plugins directory in a new `imageseo` directory
4. Run `npm install` to get the necessary npm packages
5. Run `composer install` to get the necessary composer packages
6. Activate the plugin in WordPress
7. Run `npm run build` to build the files required for the plugin. Development files are located in the `app` folder.

That's it. You're now ready to start development.

**Development Notes**

-   Ensure that you have `SCRIPT_DEBUG` enabled within your wp-config.php file. Here's a good example of wp-config.php for debugging:

    ```
     // Enable WP_DEBUG mode
    define( 'WP_DEBUG', true );

    // Enable Debug logging to the /wp-content/debug.log file
    define( 'WP_DEBUG_LOG', true );

    // Loads unminified core files
    define( 'SCRIPT_DEBUG', true );
    ```

-   Commit the `package.lock` file. Read more about why [here](https://docs.npmjs.com/files/package-lock.json).
