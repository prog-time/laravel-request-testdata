# laravel-request-testdata
PHP-библиотека для автоматической генерации тестовых данных на основе правил валидации Laravel Request.

## Установка
Установите пакет через Composer:
```bash
composer require prog-time/laravel-request-testdata
```

## Использование
Для генерации тестовых данных используйте статический метод **generate** класса **RequestDataGenerator**. Метод принимает объект Request и возвращает массив с тестовыми данными:

```php
$request = new ExampleRequest();
$testData = RequestDataGenerator::generate($request);
```

## Кастомизация тестовых данных
Если в Request-классе определить метод requestTestData, его значения будут использованы при генерации данных:

```php
class ExampleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];
    }

    public function requestTestData(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'email' => $faker->email(),
            'age' => 25,
        ];
    }
}
```
