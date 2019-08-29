# Outlook Formatter
Scan all dom and add/replace width attribute, make sure table/img in outlook desktop will be working

**Only affect outlook desktop**

## How to use

### Laravel 5.5
```php
    use OutlookFormatter;
    
    $html = ''; // your html
    $result = OutlookFormatter::format($html);

```


### PHP
```php
    use GorillaDash\OutlookFormatter\Formatter;
    
    $formatter = new Formatter(800); // First argument is max width for container;
    $formatter->setAutoCenter(['table' => true, 'image' => true]); // Set all table/td/image to be center, default is false
    
    $html = ''; // your html
    $result = $formatter->format($html);

```
