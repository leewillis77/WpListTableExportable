# WP_List_Table_Exportable

## What?
WP_List_Table_Exportable is an (almost) drop-in replacement for the WP_List_Table class that lets users export the current page of data to CSV with a simple click. 

[![Alt text for your video](/assets/demo-shot.png)](https://www.youtube.com/watch?v=dFUGJP7Mpnc)

### Export link included in list table

![Screenshot of export link](/assets/screenshot-1.png?raw=true)

### CSV Output compatible with your favourite spreadsheet app

![Screenshot of resulting CSV file](/assets/screenshot-2.png?raw=true)

## Why&hellip;
I wanted to add CSV Export functionality to an existing WP_List_Table implementation in the [Cart Recovery for WordPress Pro](https://wp-cart-recovery.com/downloads/cart-recovery-wordpress-pro/) plugin.

My initial implementation required a lot of custom code, bodges, and workarounds. It generally seemed like something which should be generic, and simpler. So I gave it a go. WP_List_Table_Exportable is the result.

## How&hellip;
If you want to use the class, here's what you can do. 

### Option 1 - Using [composer](https://getcomposer.org/)

* Add the repo as a dependency:

```json
{
	"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/leewillis77/WpListTableExportable"
        }
    ],
    "require": {
        "leewillis77/WpListTableExportable": "dev-master"
    }
}
```

* Extend ```leewillis77\WpListTableExportable\WpListTableExportable``` instead of ```WP_List_Table```
* Your plugin will need to also require ```bootstrap.php``` on any request that would result in the list table being shown *as early as practical* in the request lifecycle - before any output is created

### Option 2 - manual download

* Download, or clone this repo within your plugin
* Include the main class file before you declare your list table class
* Extend ```leewillis77\WpListTableExportable\WpListTableExportable``` instead of ```WP_List_Table```
* Your plugin will need to also require bootstrap.php on any request that would result in the list table being shown *as early as practical* in the request lifecycle - before any output is created

## Customising&hellip;

By default, the CSV file will strip any HTML tags from the cell contents, and decode HTML attributes before outputting the contents to the CSV file. Sometimes you may want an alternative representation of a cell. To do that you can implement a ```column_csv_{column_id}``` callback in your extending class. This will override the standard column_{column_id} callback when the data is being output to CSV - HTML layout will be unaffected and will use the original callback. 

For example:

```php
	// The original column callback. Used when generating the HTML list table.
	function column_cart_value( $item ) {
		return $this->currency() . ' ' . $this->get_cart_value();
	}

	// The callback for outputting to CSV. Used instead of the standard callback when
	// generating the CSV file only.
	function column_csv_cart_value( $item ) {
		return $this->get_cart_value();
	}
```

If you want to exclude a column from being output in the CSV, then you can return its ID from a hidden_columns_csv() method in your class. For example:

```php
	protected function hidden_columns_csv() {
		return array(
			'excluded_column_id'
		);
	}
```

## When&hellip;
This isn't intended as a finished solution. It's extremely *icky* in places (The requirement for bootstrap.php is a prime example).

There are also several obvious feature improvements that could be made - particularly supporting outputting *all* pages, not just the current page and/or allowing a user to select which rows to export.

The class is lightly, manually tested with a simple List Table implementation - feedback on it's usefulness with more complex list tables [would be welcome](https://github.com/leewillis77/WpListTableExportable/issues). 