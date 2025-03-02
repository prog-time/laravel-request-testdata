<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

abstract class ImageGeneratorAbstract extends FileGeneratorAbstract
{

    /**
     * @param string $format
     * @param int $size
     * @param array $ruleParams
     * @return false|string|null
     */
    protected function getFakeImageData(string $format, int $size, array $ruleParams)
    {
        try {
            $dimensionsParams = $ruleParams['dimensions'] ?? [];

            $width = $dimensionsParams['width'] ?? $dimensionsParams['max_width'] ?? $dimensionsParams['min_width'] ?? 100;
            $height = $dimensionsParams['height'] ?? $dimensionsParams['max_height'] ?? $dimensionsParams['min_height'] ?? 100;

            // Создаем пустое изображение
            $image = imagecreatetruecolor($width, $height);

            // Задаем случайный фон
            $backgroundColor = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
            imagefill($image, 0, 0, $backgroundColor);

            // Добавляем текст с размерами изображения
            $textColor = imagecolorallocate($image, 0, 0, 0);
            imagestring($image, 5, 10, 10, "$width x $height", $textColor);

            // Включаем буфер вывода
            ob_start();

            // Сохраняем изображение в буфер в нужном формате
            switch (strtolower($format)) {
                case 'png':
                    imagepng($image, null, (int)($size / 10)); // PNG (качество от 0 до 9)
                    break;
                case 'gif':
                    imagegif($image);
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    imagejpeg($image, null, $size); // JPG (качество от 0 до 100)
                    break;
            }

            // Получаем содержимое буфера
            $imageData = ob_get_contents();

            // Очищаем буфер
            ob_end_clean();

            // Освобождаем память
            imagedestroy($image);

            return $imageData;
        } catch (\Exception $e) {
            return null;
        }
    }

}
