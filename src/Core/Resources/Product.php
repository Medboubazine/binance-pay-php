<?php

namespace Medboubazine\BinancePay\Core\Resources;

use Illuminate\Support\Str;

class Product
{
    /**
     * Attributes
     *
     * @var array
     */
    protected array $attributes = [];
    /**
     * __call
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $f3_chars = Str::lower(Str::substr($name, 0, 3));
        if ($f3_chars == 'set') {
            $attribute_name = Str::snake(Str::substr($name, 3, 99));
            $this->attributes[$attribute_name] = ($arguments[0] ?? null);
            return $this;
        }
        if ($f3_chars == 'get') {
            $attribute_name = Str::snake(Str::substr($name, 3, 99));
            return $this->attributes[$attribute_name] ?? null;
        }

        return call_user_func_array([$this, $name], $arguments);
    }
}
