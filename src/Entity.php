<?php

    namespace Polaris;

    use ArrayAccess;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Contracts\Support\Jsonable;
    use RuntimeException;
    use stdClass;

    class Entity implements ArrayAccess, Jsonable, Arrayable
    {
        /**
         * Entity's attributes.
         *
         * @var array
         */
        protected $attributes = [];

        /**
         * The attributes that should be cast to other types.
         *
         * @var array
         */
        protected $casts = [];

        /**
         * Entity constructor.
         *
         * @param $attributes
         */
        public function __construct($attributes)
        {
            // If the attributes is not an array of attributes but an stdClass instead, then get the public properties
            // Of the object as the attributes.
            if ($attributes instanceof stdClass) {
                $attributes = $this->getAttributesFromClass($attributes);
            }

            // Fill the attributes array.
            $this->fillAttributes($attributes);
        }

        /**
         * Returns an array with the public properties.
         *
         * @param $object
         * @return array
         */
        public function getAttributesFromClass($object): array
        {
            return get_object_vars($object);
        }

        /**
         * Fills the internal attributes array.
         *
         * @param  array  $attributes
         */
        public function fillAttributes(array $attributes = []): void
        {
            foreach ($attributes as $key => $value) {
                $this->setAttribute($key, $value);
            }
        }

        /**
         * Set the attribute and optionally casts the attribute if needed, calling other helper functions.
         *
         * @param $attribute
         * @param $value
         */
        public function setAttribute($key, $value): void
        {
            if ($this->hasCast($key)) {
                $this->attributes[$key] = $this->castAttribute($key, $value);
            }
            else {
                $this->attributes[$key] = $value;
            }
        }

        /**
         * Get an attribute from the entity.
         *
         * @param $key
         * @return mixed|void
         */
        public function getAttribute($key)
        {
            // If not key provided return void.
            if (!$key) {
                return;
            }

            // Make sure the attribute exists in the attributes array.
            if (array_key_exists($key, $this->attributes)) {
                return $this->attributes[$key];
            }
        }

        /**
         * Determine if the given attribute exists.
         *
         * @param  mixed  $offset
         * @return bool
         */
        public function offsetExists($offset): bool
        {
            return $this->getAttribute($offset) !== null;
        }

        /**
         * Get the value for a given offset.
         *
         * @param  mixed  $offset
         * @return mixed
         */
        public function offsetGet($offset)
        {
            return $this->getAttribute($offset);
        }

        /**
         * Set the value for a given offset.
         *
         * @param  mixed  $offset
         * @param  mixed  $value
         * @return void
         */
        public function offsetSet($offset, $value)
        {
            $this->setAttribute($offset, $value);
        }

        /**
         * Unset the value for a given offset.
         *
         * @param  mixed  $offset
         * @return void
         */
        public function offsetUnset($offset)
        {
            unset($this->attributes[$offset]);
        }

        /**
         * Determine if an attribute exists on the entity.
         *
         * @param  string  $key
         * @return bool
         */
        public function __isset($key)
        {
            return $this->offsetExists($key);
        }

        /**
         * Unset an attribute on the entity.
         *
         * @param  string  $key
         * @return void
         */
        public function __unset($key)
        {
            $this->offsetUnset($key);
        }

        /**
         * Returns the given attribute.
         *
         * @param $key
         * @return mixed
         */
        public function __get($key)
        {
            return $this->getAttribute($key);
        }

        /**
         * Sets attribute dynamically.
         *
         * @param $key
         * @param $value
         */
        public function __set($key, $value)
        {
            $this->setAttribute($key, $value);
        }

        /**
         * Determine if the attribute is castable to a type.
         *
         * @param $key
         * @return bool
         */
        protected function hasCast($key): bool
        {
            return array_key_exists($key, $this->casts);
        }

        /**
         * Cast the attribute creating the corresponding Entity or EntityCollection object.
         *
         * @param $key
         * @param $value
         * @return mixed
         */
        protected function castAttribute($key, $value)
        {
            return new $this->casts[$key]($value);
        }

        /**
         * Return entity's attributes.
         *
         * @return array
         */
        public function toArray()
        {
            return array_map(static function($attribute)
            {
                if($attribute instanceof Arrayable) {
                    return $attribute->toArray();
                }

                return $attribute;
            }, $this->attributes);
        }

        /**
         * Returns a json encoded representation of the entity's attributes.
         *
         * @param  int  $options
         * @return false|string
         */
        public function toJson($options = 0)
        {
            $json = json_encode($this->toArray(), $options);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new RuntimeException(json_last_error_msg());
            }

            return $json;
        }
    }