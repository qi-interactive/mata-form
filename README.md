MATA Form
==========================================

Manages forms for MATA Framework applications.

Installation
------------

- Add the module using composer: 

```json
"mata/mata-form": "~1.0.0"
```

Client
------

```php
renderForm($model, $action = 'processForm', $fieldAttributes = [], $options = ['submitButtonText'=>'Submit']) {}
```
Renders a [`Dynamic From`](https://github.com/qi-interactive/mata-form/blob/master/widgets/DynamicForm.php) based on the `$model` attribute.

Changelog
---------

## 1.0.0-alpha, May 18, 2015

- Initial release.
