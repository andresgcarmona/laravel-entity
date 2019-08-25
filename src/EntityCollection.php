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
         * EntityCollection constructor.
         *
         * @param  array  $data
         */
        public function __construct(array $data = [])
        {
            // Hydrate items array with the items passes in the data parameter.
            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Create a new intances of $this->entity type.
                    $this->items[] = new $this->entity($item);
                }
            }

            // Call parent constructor to assign items to collection.
            parent::__construct($this->items);
        }
    }