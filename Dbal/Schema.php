<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Dbal;

use Doctrine\DBAL\Schema\Schema as BaseSchema;
use Doctrine\DBAL\Connection;

/**
 * The schema used for the ACL system.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Schema extends BaseSchema
{
    protected $options;

    protected $schema;

    protected $connection;

    /**
     * Constructor.
     *
     * @param array      $options    the names for tables
     * @param Connection $connection
     */
    public function __construct(array $options, Connection $connection = null)
    {
        $this->connection = $connection;
        $this->options = $options;
    }

    protected function getSchema()
    {
        if (!$this->schema) {
            $schemaConfig = null === $$this->connection ? null : $this->connection->getSchemaManager()->createSchemaConfig();
            $this->schema = new BaseSchema(array(), array(), $schemaConfig);
            $this->addClassTable();
            $this->addSecurityIdentitiesTable();
            $this->addObjectIdentitiesTable();
            $this->addObjectIdentityAncestorsTable();
            $this->addEntryTable();
        }

        return $this->schema;
    }

    /**
     * Merges ACL schema with the given schema.
     *
     * @param BaseSchema $schema
     */
    public function addToSchema(BaseSchema $schema)
    {
        foreach ($this->getSchema()->getTables() as $table) {
            $schema->_addTable($table);
        }

        foreach ($this->getSchema()->getSequences() as $sequence) {
            $schema->_addSequence($sequence);
        }
    }

    /**
     * @return boolean
     */
    public function hasExplicitForeignKeyIndexes()
    {
        return $this->getSchema()->hasExplicitForeignKeyIndexes();
    }

    /**
     * Returns the namespaces of this schema.
     *
     * @return array A list of namespace names.
     */
    public function getNamespaces()
    {
        return $this->getSchema()->getNamespaces();
    }

    /**
     * Gets all tables of this schema.
     *
     * @return \Doctrine\DBAL\Schema\Table[]
     */
    public function getTables()
    {
        return $this->getSchema()->getTables();
    }

    /**
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Table
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getTable($tableName)
    {
        return $this->getSchema()->getTable($tableName);
    }

    /**
     * Does this schema have a namespace with the given name?
     *
     * @param string $namespaceName
     *
     * @return boolean
     */
    public function hasNamespace($namespaceName)
    {
        return $this->getSchema()->hasNamespace($namespaceName);
    }

    /**
     * Does this schema have a table with the given name?
     *
     * @param string $tableName
     *
     * @return boolean
     */
    public function hasTable($tableName)
    {
        return $this->getSchema()->hasTable($tableName);
    }

    /**
     * Gets all table names, prefixed with a schema name, even the default one if present.
     *
     * @return array
     */
    public function getTableNames()
    {
        return $this->getSchema()->getTableNames();
    }

    /**
     * @param string $sequenceName
     *
     * @return boolean
     */
    public function hasSequence($sequenceName)
    {
        return $this->getSchema()->hasSequence($sequenceName);
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\Sequence
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getSequence($sequenceName)
    {
        return $this->getSchema()->getSequence($sequenceName);
    }

    /**
     * @return \Doctrine\DBAL\Schema\Sequence[]
     */
    public function getSequences()
    {
        return $this->getSchema()->getSequences();
    }

    /**
     * Creates a new namespace.
     *
     * @param string $namespaceName The name of the namespace to create.
     *
     * @return \Doctrine\DBAL\Schema\Schema This schema instance.
     */
    public function createNamespace($namespaceName)
    {
        $this->getSchema()->createNamespace($namespaceName);

        return $this;
    }

    /**
     * Creates a new table.
     *
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function createTable($tableName)
    {
        return $this->getSchema()->createTable($tableName);
    }

    /**
     * Renames a table.
     *
     * @param string $oldTableName
     * @param string $newTableName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function renameTable($oldTableName, $newTableName)
    {
        $this->getSchema()->renameTable($oldTableName, $newTableName);

        return $this;
    }

    /**
     * Drops a table from the schema.
     *
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function dropTable($tableName)
    {
        $this->getSchema()->dropTable($tableName);

        return $this;
    }

    /**
     * Creates a new sequence.
     *
     * @param string  $sequenceName
     * @param integer $allocationSize
     * @param integer $initialValue
     *
     * @return \Doctrine\DBAL\Schema\Sequence
     */
    public function createSequence($sequenceName, $allocationSize=1, $initialValue=1)
    {
        return $this->getSchema()->createSequence($sequenceName, $allocationSize, $initialValue);
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function dropSequence($sequenceName)
    {
        $this->getSchema()->dropSequence($sequenceName);

        return $this;
    }

    /**
     * Returns an array of necessary SQL queries to create the schema on the given platform.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toSql(AbstractPlatform $platform)
    {
        return $this->getSchema()->toSql($platform);
    }

    /**
     * Return an array of necessary SQL queries to drop the schema on the given platform.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toDropSql(AbstractPlatform $platform)
    {
        return $this->getSchema()->toDropSql($platform);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema              $toSchema
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function getMigrateToSql(Schema $toSchema, AbstractPlatform $platform)
    {
        return $this->getSchema()->getMigrateToSql($toSchema, $platform);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema              $fromSchema
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function getMigrateFromSql(Schema $fromSchema, AbstractPlatform $platform)
    {
        return $this->getSchema()->getMigrateFromSql($fromSchema, $platform);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Visitor\Visitor $visitor
     *
     * @return void
     */
    public function visit(Visitor $visitor)
    {
        $this->getSchema()->visit($visitor);
    }

    /**
     * Cloning a Schema triggers a deep clone of all related assets.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->schema) {
            $this->schema = clone $this->schema;
        }
    }


    /**
     * Adds the class table to the schema.
     */
    protected function addClassTable()
    {
        $table = $this->schema->createTable($this->options['class_table_name']);
        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('class_type', 'string', array('length' => 200));
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('class_type'));
    }

    /**
     * Adds the entry table to the schema.
     */
    protected function addEntryTable()
    {
        $table = $this->schema->createTable($this->options['entry_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('class_id', 'integer', array('unsigned' => true));
        $table->addColumn('object_identity_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $table->addColumn('field_name', 'string', array('length' => 50, 'notnull' => false));
        $table->addColumn('ace_order', 'smallint', array('unsigned' => true));
        $table->addColumn('security_identity_id', 'integer', array('unsigned' => true));
        $table->addColumn('mask', 'integer');
        $table->addColumn('granting', 'boolean');
        $table->addColumn('granting_strategy', 'string', array('length' => 30));
        $table->addColumn('audit_success', 'boolean');
        $table->addColumn('audit_failure', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('class_id', 'object_identity_id', 'field_name', 'ace_order'));
        $table->addIndex(array('class_id', 'object_identity_id', 'security_identity_id'));

        $table->addForeignKeyConstraint($this->schema->getTable($this->options['class_table_name']), array('class_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($this->schema->getTable($this->options['oid_table_name']), array('object_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($this->schema->getTable($this->options['sid_table_name']), array('security_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
    }

    /**
     * Adds the object identity table to the schema.
     */
    protected function addObjectIdentitiesTable()
    {
        $table = $this->schema->createTable($this->options['oid_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('class_id', 'integer', array('unsigned' => true));
        $table->addColumn('object_identifier', 'string', array('length' => 100));
        $table->addColumn('parent_object_identity_id', 'integer', array('unsigned' => true, 'notnull' => false));
        $table->addColumn('entries_inheriting', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('object_identifier', 'class_id'));
        $table->addIndex(array('parent_object_identity_id'));

        $table->addForeignKeyConstraint($table, array('parent_object_identity_id'), array('id'));
    }

    /**
     * Adds the object identity relation table to the schema.
     */
    protected function addObjectIdentityAncestorsTable()
    {
        $table = $this->schema->createTable($this->options['oid_ancestors_table_name']);

        $table->addColumn('object_identity_id', 'integer', array('unsigned' => true));
        $table->addColumn('ancestor_id', 'integer', array('unsigned' => true));

        $table->setPrimaryKey(array('object_identity_id', 'ancestor_id'));

        $oidTable = $this->schema->getTable($this->options['oid_table_name']);
        $table->addForeignKeyConstraint($oidTable, array('object_identity_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
        $table->addForeignKeyConstraint($oidTable, array('ancestor_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
    }

    /**
     * Adds the security identity table to the schema.
     */
    protected function addSecurityIdentitiesTable()
    {
        $table = $this->schema->createTable($this->options['sid_table_name']);

        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('identifier', 'string', array('length' => 200));
        $table->addColumn('username', 'boolean');

        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('identifier', 'username'));
    }
}
