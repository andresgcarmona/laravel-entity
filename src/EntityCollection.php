<?php

    namespace Polaris;

    use Illuminate\Support\Collection;

    /**
     * Generic Entity collection class.
     *
     * Class EntityCollection
     *
     * @package Polaris
     */
    class EntityCollection extends Collection
    {
        /**
         * The items of the collection.
         *
         * @var array
         */
        protected $items = [];

        /**
         * The name of the element where this collection is going to get the items.
         *
         * @var string
         */
        protected $key = 'items';

        /**
         * Extra attributes to be added to the collection.
         *
         * @var array
         */
        protected $extras = [];

        /**
         * EntityCollection constructor.
         *
         * @param  array  $data
         */
        public function __construct(array $data = [])
        {
            // Hydrate items array with the items passes in the data parameter.
            foreach ($data as $item) {
                // Create a new instances of $this->entity type.
                $this->items[] = new $this->entity($item);
            }

            // Hydrate extra attributes.
            $this->mutateExtraAttributes($data);

            // Call parent constructor to assign items to collection.
            parent::__construct($this->items);
        }

        /**
         * Returns the name of the key for this collection.
         *
         * @return string
         */
        public function getKey()
        {
            return $this->key;
        }

        /**
         * Returns the extra attributes array.
         *
         * @return array
         */
        public function getExtras()
        {
            return $this->extras;
        }

        /**
         * Updates extra attributes array, assigning the value of the attribute in data to the extra attribute
         * with the given key.
         *
         * @param  array  $data
         */
        public function mutateExtraAttributes(array $data)
        {
            foreach ($this->getExtras() as $key => $attribute) {
                if (isset($data[$attribute])) {
                    $this->$attribute = $data[$attribute];
                }
            }
        }

        /**
         * Get the collection of items as a plain array.
         * Merges the items with the extra attributes.
         *
         * @return array
         */
        public function toArray()
        {
            // Calls parent toArray method to get array of items.
            $items = parent::toArray();

            // Merges items and extras attributes.
            return array_merge(['items' => $items], $this->getExtras());
        }

        public function __toString()
        {
            return $this->toJson();
        }
    }