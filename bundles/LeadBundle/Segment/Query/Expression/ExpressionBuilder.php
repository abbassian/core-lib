<?php

namespace Autoborna\LeadBundle\Segment\Query\Expression;

use Doctrine\DBAL\Connection;
use Autoborna\LeadBundle\Segment\Exception\SegmentQueryException;

/**
 * ExpressionBuilder class is responsible to dynamically create SQL query parts.
 *
 * @see   www.doctrine-project.org
 * @since  2.1
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class ExpressionBuilder
{
    const EQ      = '=';
    const NEQ     = '<>';
    const LT      = '<';
    const LTE     = '<=';
    const GT      = '>';
    const GTE     = '>=';
    const REGEXP  = 'REGEXP';
    const BETWEEN = 'BETWEEN';

    /**
     * The DBAL Connection.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * Initializes a new <tt>ExpressionBuilder</tt>.
     *
     * @param \Doctrine\DBAL\Connection $connection the DBAL Connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Creates a conjunction of the given boolean expressions.
     *
     * Example:
     *
     *     [php]
     *     // (u.type = ?) AND (u.role = ?)
     *     $expr->andX('u.type = ?', 'u.role = ?'));
     *
     * @param mixed $x Optional clause. Defaults = null, but requires
     *                 at least one defined when converting to string.
     *
     * @return \Autoborna\LeadBundle\Segment\Query\Expression\CompositeExpression
     */
    public function andX($x = null)
    {
        if (is_array($x)) {
            return new CompositeExpression(CompositeExpression::TYPE_AND, $x);
        }

        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    /**
     * Creates a disjunction of the given boolean expressions.
     *
     * Example:
     *
     *     [php]
     *     // (u.type = ?) OR (u.role = ?)
     *     $qb->where($qb->expr()->orX('u.type = ?', 'u.role = ?'));
     *
     * @param mixed $x Optional clause. Defaults = null, but requires
     *                 at least one defined when converting to string.
     *
     * @return \Autoborna\LeadBundle\Segment\Query\Expression\CompositeExpression
     */
    public function orX($x = null)
    {
        if (is_array($x)) {
            return new CompositeExpression(CompositeExpression::TYPE_OR, $x);
        }

        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }

    /**
     * Creates a comparison expression.
     *
     * @param mixed  $x        the left expression
     * @param string $operator one of the ExpressionBuilder::* constants
     * @param mixed  $y        the right expression
     *
     * @return string
     */
    public function comparison($x, $operator, $y)
    {
        return $x.' '.$operator.' '.$y;
    }

    /**
     * Creates a between comparison expression.
     *
     * @param $x
     * @param $arr
     *
     * @throws SegmentQueryException
     *
     * @return string
     */
    public function between($x, $arr)
    {
        if (!is_array($arr) || 2 != count($arr)) {
            throw new SegmentQueryException('Between expression expects second argument to be an array with exactly two elements');
        }

        return $x.' '.self::BETWEEN.' '.$this->comparison($arr[0], 'AND', $arr[1]);
    }

    /**
     * Creates a not between comparison expression.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param $x
     * @param $arr
     *
     * @throws SegmentQueryException
     *
     * @return string
     */
    public function notBetween($x, $arr)
    {
        return 'NOT '.$this->between($x, $arr);
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function regexp($x, $y)
    {
        return $this->comparison($x, self::REGEXP, $y);
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function notRegexp($x, $y)
    {
        return 'NOT '.$this->comparison($x, self::REGEXP, $y);
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     *     [php]
     *     // u.id = ?
     *     $expr->eq('u.id', '?');
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function eq($x, $y)
    {
        return $this->comparison($x, self::EQ, $y);
    }

    /**
     * Creates a non equality comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <> <right expr>. Example:.
     *
     *     [php]
     *     // u.id <> 1
     *     $q->where($q->expr()->neq('u.id', '1'));
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function neq($x, $y)
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    /**
     * Creates a lower-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> < <right expr>. Example:.
     *
     *     [php]
     *     // u.id < ?
     *     $q->where($q->expr()->lt('u.id', '?'));
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function lt($x, $y)
    {
        return $this->comparison($x, self::LT, $y);
    }

    /**
     * Creates a lower-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <= <right expr>. Example:.
     *
     *     [php]
     *     // u.id <= ?
     *     $q->where($q->expr()->lte('u.id', '?'));
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function lte($x, $y)
    {
        return $this->comparison($x, self::LTE, $y);
    }

    /**
     * Creates a greater-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> > <right expr>. Example:.
     *
     *     [php]
     *     // u.id > ?
     *     $q->where($q->expr()->gt('u.id', '?'));
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function gt($x, $y)
    {
        return $this->comparison($x, self::GT, $y);
    }

    /**
     * Creates a greater-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> >= <right expr>. Example:.
     *
     *     [php]
     *     // u.id >= ?
     *     $q->where($q->expr()->gte('u.id', '?'));
     *
     * @param mixed $x the left expression
     * @param mixed $y the right expression
     *
     * @return string
     */
    public function gte($x, $y)
    {
        return $this->comparison($x, self::GTE, $y);
    }

    /**
     * Creates an IS NULL expression with the given arguments.
     *
     * @param string $x the field in string format to be restricted by IS NULL
     *
     * @return string
     */
    public function isNull($x)
    {
        return $x.' IS NULL';
    }

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     *
     * @param string $x the field in string format to be restricted by IS NOT NULL
     *
     * @return string
     */
    public function isNotNull($x)
    {
        return $x.' IS NOT NULL';
    }

    /**
     * Creates a LIKE() comparison expression with the given arguments.
     *
     * @param string $x field in string format to be inspected by LIKE() comparison
     * @param mixed  $y argument to be used in LIKE() comparison
     *
     * @return string
     */
    public function like($x, $y)
    {
        return $this->comparison($x, 'LIKE', $y);
    }

    /**
     * Creates a NOT LIKE() comparison expression with the given arguments.
     *
     * @param string $x field in string format to be inspected by NOT LIKE() comparison
     * @param mixed  $y argument to be used in NOT LIKE() comparison
     *
     * @return string
     */
    public function notLike($x, $y)
    {
        return $this->comparison($x, 'NOT LIKE', $y);
    }

    /**
     * Creates a IN () comparison expression with the given arguments.
     *
     * @param string       $x the field in string format to be inspected by IN() comparison
     * @param string|array $y the placeholder or the array of values to be used by IN() comparison
     *
     * @return string
     */
    public function in($x, $y)
    {
        return $this->comparison($x, 'IN', '('.implode(', ', (array) $y).')');
    }

    /**
     * Creates a NOT IN () comparison expression with the given arguments.
     *
     * @param string       $x the field in string format to be inspected by NOT IN() comparison
     * @param string|array $y the placeholder or the array of values to be used by NOT IN() comparison
     *
     * @return string
     */
    public function notIn($x, $y)
    {
        return $this->comparison($x, 'NOT IN', '('.implode(', ', (array) $y).')');
    }

    /**
     * Quotes a given input parameter.
     *
     * @param mixed       $input the parameter to be quoted
     * @param string|null $type  the type of the parameter
     *
     * @return string
     */
    public function literal($input, $type = null)
    {
        return $this->connection->quote($input, $type);
    }

    /**
     * Puts argument into EXISTS mysql function.
     *
     * @param $input
     *
     * @return string
     */
    public function exists($input)
    {
        return $this->func('EXISTS', $input);
    }

    /**
     * Puts argument into NOT EXISTS mysql function.
     *
     * @param $input
     *
     * @return string
     */
    public function notExists($input)
    {
        return $this->func('NOT EXISTS', $input);
    }

    /**
     * Creates a functional expression.
     *
     * @param string       $func any function to be applied on $x
     * @param mixed        $x    the left expression
     * @param string|array $y    the placeholder or the array of values to be used by IN() comparison
     *
     * @return string
     */
    public function func($func, $x, $y = null)
    {
        $functionArguments = func_get_args();
        $additionArguments = array_splice($functionArguments, 2);

        foreach ($additionArguments as $k=> $v) {
            $additionArguments[$k] = is_numeric($v) && intval($v) === $v ? $v : $this->literal($v);
        }

        return $func.'('.$x.(count($additionArguments) ? ', ' : '').join(',', $additionArguments).')';
    }
}
