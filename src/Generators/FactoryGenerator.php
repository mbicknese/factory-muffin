<?php

/*
 * This file is part of Factory Muffin.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace League\FactoryMuffin\Generators;

use League\FactoryMuffin\FactoryMuffin;

/**
 * This is the factory generator class.
 *
 * The factory generator can be useful for setting up relationships between
 * models. The factory generator will return the model id of the model you ask
 * it to generate.
 *
 * @author  Graham Campbell <graham@mineuk.com>
 * @author  Scott Robertson <scottymeuk@gmail.com>
 * @license <https://github.com/thephpleague/factory-muffin/blob/master/LICENSE> MIT
 */
class FactoryGenerator implements GeneratorInterface
{
    /**
     * The kind of attribute that will be generated.
     *
     * @var string
     */
    private $kind;

    /**
     * The model instance.
     *
     * @var object
     */
    private $model;

    /**
     * The factory muffin instance.
     *
     * @var \League\FactoryMuffin\FactoryMuffin
     */
    private $factoryMuffin;

    /**
     * Generate, and return the attribute.
     *
     * @var string[]
     */
    private static $methods = ['getKey', 'pk'];

    /**
     * The factory properties.
     *
     * @var string[]
     */
    private static $properties = ['id', '_id'];

    /**
     * Create a new factory generator instance.
     *
     * @param string                              $kind          The kind of attribute.
     * @param object                              $model         The model instance.
     * @param \League\FactoryMuffin\FactoryMuffin $factoryMuffin The factory muffin instance.
     *
     * @return void
     */
    public function __construct($kind, $model, FactoryMuffin $factoryMuffin)
    {
        $this->kind = $kind;
        $this->model = $model;
        $this->factoryMuffin = $factoryMuffin;
    }

    /**
     * Generate, and return the attribute.
     *
     * The value returned is the id of the generated model, if applicable.
     *
     * @return int|null
     */
    public function generate()
    {
        $name = substr($this->kind, 8);

        $model = $this->factory($name);

        return $this->getId($model);
    }

    /**
     * Create an instance of the model.
     *
     * This model will be automatically saved to the database if the model we
     * are generating it for has been saved (the create function was used).
     *
     * @param string $name The model definition name.
     *
     * @return object
     */
    private function factory($name)
    {
        if ($this->factoryMuffin->isPendingOrSaved($this->model)) {
            return $this->factoryMuffin->create($name);
        }

        return $this->factoryMuffin->instance($name);
    }

    /**
     * Get the model id.
     *
     * @param object $model The model instance.
     *
     * @return int|null
     */
    private function getId($model)
    {
        // Check to see if we can get an id via our defined methods
        foreach (self::$methods as $method) {
            if (method_exists($model, $method)) {
                return $model->$method();
            }
        }

        // Check to see if we can get an id via our defined properties
        foreach (self::$properties as $property) {
            if (isset($model->$property)) {
                return $model->$property;
            }
        }
    }
}