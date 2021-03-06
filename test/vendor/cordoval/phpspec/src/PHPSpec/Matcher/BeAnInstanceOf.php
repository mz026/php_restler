<?php
/**
 * PHPSpec
 *
 * LICENSE
 *
 * This file is subject to the GNU Lesser General Public License Version 3
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/lgpl-3.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpspec.net so we can send you a copy immediately.
 *
 * @category  PHPSpec
 * @package   PHPSpec
 * @copyright Copyright (c) 2007-2009 Pádraic Brady, Travis Swicegood
 * @copyright Copyright (c) 2010-2012 Pádraic Brady, Travis Swicegood,
 *                                    Marcello Duarte
 * @license   http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public Licence Version 3
 */
namespace PHPSpec\Matcher;

/**
 * @see \PHPSpec\Matcher
 */
use \PHPSpec\Matcher;

/**
 * @category   PHPSpec
 * @package    PHPSpec
 * @copyright  Copyright (c) 2007-2009 Pádraic Brady, Travis Swicegood
 * @copyright  Copyright (c) 2010-2012 Pádraic Brady, Travis Swicegood,
 *                                     Marcello Duarte
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public Licence Version 3
 */
class BeAnInstanceOf implements Matcher
{

    /**
     * The expected value
     * 
     * @var mixed
     */
    protected $_expected = null;

    /**
     * The actual value
     * 
     * @var object
     */
    protected $_actual = null;

    /**
     * Matcher is usually constructed with the expected value
     * 
     * @param string $expected
     */
    public function __construct($expected)
    {
        $this->_expected = $expected;
    }

    /**
     * Checks whether actual value is an instance of expected
     * 
     * @param mixed $expected
     * @return boolean
     */
    public function matches($actual)
    {
        if ($actual instanceof $this->_expected) {
            $this->_actual = $this->_expected;
            return true;
        } else {
            if (is_object($actual)) {
                $this->_actual = get_class($actual);
            } elseif (is_null($actual)) {
                $this->_actual = 'NULL';
            } else {
                $this->_actual = $actual;
            }
        }
        return false;
    }

    /**
     * Returns failure message in case we are using should
     * 
     * @return string
     */
    public function getFailureMessage()
    {
        return 'expected ' . var_export($this->_expected, true) . ', got ' .
               $this->export($this->_actual) . ' (using beAnInstanceOf())';
    }

    /**
     * Returns failure message in case we are using should not
     * 
     * @return string
     */
    public function getNegativeFailureMessage()
    {
        return 'expected ' . var_export($this->_actual, true) . ' not to be ' .
               $this->export($this->_expected) . ' (using beAnInstanceOf())';
    }

    /**
     * Returns the matcher description
     * 
     * @return string
     */
    public function getDescription()
    {
        return 'be an instance of ' . $this->export($this->_expected);
    }
    
    private function export($value)
    {
        return str_replace('\\\\', '\\', var_export($value, true));
    }
}