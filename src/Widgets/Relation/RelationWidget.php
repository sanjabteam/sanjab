<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Sanjab\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Exception;

/**
 * Belongs to relation picker.
 *
 * @method $this    withNull(string $val)           null option title.
 * @method $this    query(callable $val)            callback to modify related items query.
 * @method $this    searchFields(array $val)        array of fields to search.
 * @method $this    optionsLabelKey(string $val)    options label key.
 * @method $this    relationKey(string $val)        relation key.
 * @method $this    model(string $val)              model to handle
 * @method $this    format(string $val)             show format. example: ('%id - %name')
 */
abstract class RelationWidget extends Widget
{
    protected $getters = [
        'options'
    ];

    protected $tempModelInstance = null;

    public function init()
    {
        $this->query(function ($query) {
        });
        $this->tag('select-widget');
        $this->setProperty("format", '%id');
        $this->optionsLabelKey('label');
        $this->relationKey('relatedKey');
        $this->withNull(null);
    }

    public function postInit()
    {
        if (empty($this->property('searchFields'))) {
            $matches = null;
            preg_match_all("/%([A-Za-z0-9_]+)/", $this->property('format'), $matches);
            $searchFields = [];
            foreach ($matches[1] as $match) {
                if (method_exists($this->getModelInstance(), 'isTranslationAttribute') && $this->getModelInstance()->isTranslationAttribute($match)) {
                    $match = 'translations.'.$match;
                }
                $searchFields[] = $match;
            }
            $this->searchFields($searchFields);
        }
    }

    protected function store(Request $request, Model $item)
    {
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
    }

    protected function search(Builder $query, string $search, string $type = null)
    {
        foreach ($this->property('searchFields') as $searchField) {
            $relation = preg_replace('/\.[A-Za-z0-9_]+$/', '', $this->property("name").'.'.$searchField);
            $field = str_replace($relation.'.', '', $this->property("name").'.'.$searchField);
            $query->orWhereHas($relation, function (Builder $query) use ($field, $search) {
                $query->where($field, "LIKE", "%".$search."%");
            });
        }
    }

    protected function order(Builder $query, string $key, string $direction = 'asc')
    {
        $query->select($this->table.".*")
            ->leftJoin($this->relatedTable. " as __".$this->relatedTable, $this->tempModel->getTable().".".$this->foreignKey, "=", "__".$this->relatedTable.".".$this->ownerKey)
            ->orderBy("__".$this->relatedTable.".".$this->property("orderColumn"), $direction);
    }

    /**
     * Get model name.
     *
     * @return string
     */
    protected function getModel()
    {
        $model = null;
        if (! empty($this->property('model'))) {
            $model = $this->property('model');
        } elseif (isset($this->controllerProperties['model'])) {
            $model= $this->controllerProperties['model'];
        } else {
            throw new Exception("You need to set model for '".$this->property('name')."'");
        }
        return $model;
    }

    /**
     * Get instance of model object.
     *
     * @return Model
     */
    public function getModelInstance()
    {
        if ($this->tempModelInstance == null) {
            $this->tempModelInstance = new $this->model;
        }
        return $this->tempModelInstance;
    }

    /**
     * Get related model name.
     *
     * @return string
     */
    public function getRelatedModel()
    {
        return get_class($this->modelInstance->{ $this->property("name") }()->getRelated());
    }

    /**
     * Get related model table name.
     *
     * @return string
     */
    public function getRelatedModelTable()
    {
        return $this->modelInstance->{ $this->property("name") }()->getRelated()->getTable();
    }

    /**
     * Get related model key name.
     *
     * @return string
     */
    public function getRelatedKey()
    {
        return $this->modelInstance->{ $this->property("name") }()->getRelated()->getKeyName();
    }

    /**
     * Get relation foreign key.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->modelInstance->{ $this->property("name") }()->getForeignKey();
    }
    /**
     * Get model owner key.
     *
     * @return string
     */
    public function getOwnerKey()
    {
        return $this->modelInstance->{ $this->property("name") }()->getOwnerKeyName();
    }

    /**
     * Get availble options.
     *
     * @return array
     */
    public function getOptions()
    {
        $out = [];
        if ($this->property('withNull')) {
            $out[] = [$this->property('optionsLabelKey') => $this->property('withNull'), 'value' => null];
        }
        $options = $this->relatedModel::query();
        $this->property("query")($options);
        $options = $options->get();
        foreach ($options as $option) {
            $text = $this->property("format");
            preg_match_all("/%([A-Za-z0-9_]+)/", $text, $matches);
            foreach ($matches[1] as $match) {
                $text = str_replace("%".$match, $option->{ $match }, $text);
            }
            $out[] = [$this->property('optionsLabelKey') => $text, 'value' => $option->{ $this->{$this->relationKey} }];
        }
        return $out;
    }
}
