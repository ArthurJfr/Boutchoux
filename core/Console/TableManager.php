<?php

namespace Core\Console;

use Core\App;
use PDO;

/**
 * @author Henry Malo
 */
class TableManager
{
    // Couleurs de la console
    const COLORS = [
        'RESET' => "\033[0m",
        'RED' => "\033[31m",
        'GREEN' => "\033[32m",
        'YELLOW' => "\033[33m",
        'BLUE' => "\033[34m",
        'MAGENTA' => "\033[35m",
        'CYAN' => "\033[36m",
        'WHITE' => "\033[37m",
    ];

    // Commandes disponibles
    private const AVAILABLE_COMMANDS = [
        "Make Table " . self::COLORS['GREEN'] . "-- For Create new table in BDD",
        "Show Table " . self::COLORS['GREEN'] . "-- For view table in BDD",
        "Update Table " . self::COLORS['GREEN'] . "-- For Update and clean table in BDD",
        "Delete Table " . self::COLORS['GREEN'] . "-- For Delete table in BDD",
        "Add Fixture " . self::COLORS['GREEN'] . "-- For add data for the table in BDD",
    ];

    public function run(array $argv): void
    {
        // Si la commande est 'help'
        if (!empty($argv[1]) && $argv[1] === 'Help') {
            $this->displayCommands();
            return;
        }

        // Si la commande est 'Make Table'
        if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] === 'Make' && $argv[2] === 'Table') {
            $this->createTable();
            return;
        }

        // Si la commande est 'Update Table'
        if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] === 'Update' && $argv[2] === 'Table') {
            $this->updateTable();
            return;
        }

        // Si la commande est 'Delete Table'
        if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] === 'Delete' && $argv[2] === 'Table') {
            $this->deleteTable();
            return;
        }

        if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] === 'Show' && $argv[2] === 'Table') {
            $this->showTable();
            return;
        }

        if (!empty($argv[1]) && !empty($argv[2]) && $argv[1] === 'Add' && $argv[2] === 'Fixture') {
            $this->AddFixtures();
            return;
        }




        // Si aucune commande valide n'a été trouvée, afficher la liste des commandes disponibles
        $this->displayCommands();
    }

    // Fonction pour afficher la liste des commandes disponibles
    private function displayCommands(): void
    {
        echo "Liste des commandes disponibles : \n";
        foreach (self::AVAILABLE_COMMANDS as $command) {
            echo self::COLORS['CYAN'] . "php bin/console.php $command" . self::COLORS['RESET'] . "\n";
        }
    }


    private function createTable(): void
    {
        $app = App::getInstance();

        if (file_exists('config/config.php')) {
            usleep(100000);


            // Demander les informations sur la table
            echo self::COLORS['YELLOW'] . "Nom de la table : " . self::COLORS['RESET'] . "\n";
            $table_name = trim(fgets(STDIN));

            if (empty($table_name)) {
                echo self::COLORS['RED'] . "Nom de la table invalide" . self::COLORS['RESET'] . "\n";
                return;
            }

            echo self::COLORS['YELLOW'] . "Nombre de colonnes : " . self::COLORS['RESET'] . "\n";
            echo self::COLORS['WHITE'] . "La colonne id est créé automatiquement sur la table. " . self::COLORS['RESET'] . "\n";
            $num_columns = trim(fgets(STDIN));

            $columns = array();

            for ($i = 1; $i <= $num_columns; $i++) {
                echo "\nColonne $i\n";

                echo self::COLORS['YELLOW'] . "Nom : ". self::COLORS['RESET'] . "\n";
                $column_name = trim(fgets(STDIN));

                echo self::COLORS['YELLOW'] . "Entrez le type de la colonne : " . self::COLORS['RESET'] . "\n";
                $column_type = trim(fgets(STDIN));

                echo self::COLORS['YELLOW'] . "Entrez la taille du type de la colonne (laissez vide pour un type non numérique) : " . self::COLORS['RESET'] . "\n";
                $column_type_size = trim(fgets(STDIN));

                if ($column_type_size !== '') {
                    $column_type = $column_type . '(' . $column_type_size . ')';
                }

                echo "Type : " . $column_type . "\n";


                echo self::COLORS['YELLOW'] . "Valeur par défaut : ". self::COLORS['RESET'] . "\n";
                $column_default = trim(fgets(STDIN));

                $column_auto_increment = false;

                if ($column_name === 'id') {
                    echo self::COLORS['YELLOW'] . "Auto-incrémentée ? (tapez 'oui' ou 'non') : ". self::COLORS['RESET'] . "\n";
                    $column_auto_increment = strtolower(trim(fgets(STDIN))) === 'oui';
                }



                $column = [
                    'name' => $column_name,
                    'type' => $column_type,
                    'default' => $column_default,
                    'auto_increment' => $column_auto_increment,
                ];

                $columns[] = $column;
            }

            // Générer le contenu du fichier de la table
            $table_content = "<?php\n\n";
            $table_content .= "return [\n";
            $table_content .= "    'tableconfig' => [\n";
            $table_content .= "    'columns' => [\n";

            $table_content .= "        'id' => [\n";
            $table_content .= "            'type' => 'int(11)',\n";
            $table_content .= "            'default' => '',\n";
            $table_content .= "            'auto_increment' => true\n";
            $table_content .= "         ],\n";

            foreach ($columns as $column) {
                $table_content .= "        '{$column['name']}' => [\n";
                $table_content .= "            'type' => '{$column['type']}',\n";
                $table_content .= "            'default' => '{$column['default']}',\n";
                $table_content .= "            'auto_increment' => " . ($column['auto_increment'] ? 'true' : 'false') . "\n";
                $table_content .= "        ],\n";
            }

            $table_content .= "    ],\n";
            $table_content .= "    ],\n";
            $table_content .= "];\n";

            // Écrire le fichier de la table
            $table_dir = 'config/tables';
            if (!is_dir($table_dir) && !mkdir($table_dir)) {
                echo "Erreur : impossible de créer le répertoire des tables.\n";
                exit;
            }


            // Générer le contenu du fichier de la tableFixture
            $tablefixture_content = "<?php\n\n";
            $tablefixture_content .= "return [\n";
            $tablefixture_content .= "    'table' => [\n";
            for ($i = 1; $i <= 2; $i++) {
                $fixture_number = "fixture" . $i;
                $tablefixture_content .= "    '{$fixture_number}' => [\n";
                $tablefixture_content .= "        'columns' => [\n";

                foreach ($columns as $column) {
                    $tablefixture_content .= "            [\n";
                    $tablefixture_content .= "                'name' => '{$column['name']}',\n";
                    $tablefixture_content .= "                'data' => 'Entrer la data ici',\n";
                    $tablefixture_content .= "            ],\n";
                }

                $tablefixture_content .= "        ],\n";
                $tablefixture_content .= "    ],\n";
            }

            $tablefixture_content .= "    ],\n";
            $tablefixture_content .= "];\n";

            // Écrire le fichier de la table
            $tablefixture_dir = 'config/tablesfixtures';
            if (!is_dir($tablefixture_dir) && !mkdir($tablefixture_dir)) {
                echo "Erreur : impossible de créer le répertoire des tables.\n";
                exit;
            }



            $tablefixture_file_path = $tablefixture_dir . '/' . $table_name . '.php';
            $table_file_path = $table_dir . '/' . $table_name . '.php';

            if (file_put_contents($table_file_path, $table_content) !== false) {
                echo self::COLORS['GREEN'] . "Le fichier de la table a été créé avec succès !" . self::COLORS['RESET'] . "\n";
            } else {
                echo self::COLORS['RED'] . "Erreur : impossible de créer le fichier de la table" . self::COLORS['RESET'] . "\n";
            }
            if (file_put_contents($tablefixture_file_path, $tablefixture_content) !== false) {
                echo self::COLORS['GREEN'] . "Le fichier de la tablefixture a été créé avec succès !" . self::COLORS['RESET'] . "\n";
            } else {
                echo self::COLORS['RED'] . "Erreur : impossible de créer le fichier de la tablefixture" . self::COLORS['RESET'] . "\n";
            }
        } else {
            echo self::COLORS['RED'] . "Erreur : le fichier de configuration n'existe pas" . self::COLORS['RESET'] . "\n";
        }





        //Vérifie et selectionne les fichiers php.
        $configDir = 'config/tables';
        $configFiles = array_diff(scandir($configDir), array('..', '.'));

        foreach ($configFiles as $configFile) {
            $configFilePath = $configDir . '/' . $configFile;
            $configFileInfo = pathinfo($configFilePath);
            if ($configFileInfo['extension'] === 'php') {
                $tableName = $configFileInfo['filename'];
                $tablesConfig = require_once $configFilePath;


                // Vérifie si la table existe déjà
                $rows = $app->getDataBase()->query("SHOW TABLES", 'stdClass');
                $tables = array_map(function($row) {
                    return $row->Tables_in_mvc6_tablecreator;
                }, $rows);

                if (in_array($tableName, $tables)) {
                    echo '';
                    continue;
                }


                //déclenche le foreach de chaque table.
                foreach ($tablesConfig['tableconfig'] as $tableInfo) {
                    $columnDefinitions = array();
                    foreach ($tableInfo as $columnName => $columnInfo) {
                        $columnType = $columnInfo['type'];
                        $columnDefault = $columnInfo['default'] !== '' ? "DEFAULT '{$columnInfo['default']}'" : '';
                        $columnAutoIncrement = $columnInfo['auto_increment'] ? 'AUTO_INCREMENT' : '';
                        $columnDefinitions[] = "$columnName $columnType $columnDefault $columnAutoIncrement";
                        if ($columnAutoIncrement) {
                            $columnDefinitions[] = "PRIMARY KEY ($columnName)";
                        }
                    }
                    //insert BDD
                    $query = "CREATE TABLE $tableName (".implode(",", $columnDefinitions).")";
                    $app->getDatabase()->prepareInsert($query, array());
                    echo self::COLORS['GREEN'] . "La table " . self::COLORS['CYAN'] . $tableName . self::COLORS['GREEN'] . " a été créée avec succès.". self::COLORS['RESET'] . "\n";
                }
            }
        }
        // Demander si l'utilisateur souhaite créer une table
        echo self::COLORS['YELLOW'] . "Voulez-vous créer une autre table ? (oui/non) : " . self::COLORS['RESET'] . "\n";
        $answer = strtolower(trim(fgets(STDIN)));

        if ($answer == 'oui') {
            $this->createTable();
        }

    }

    private function updateTable(){
        $app = \Core\App::getInstance();
        //Vérifie et selectionne les fichiers php.
        $configDir = 'config/tables';
        $configFiles = array_diff(scandir($configDir), array('..', '.'));
        foreach ($configFiles as $configFile) {
            $configFilePath = $configDir . '/' . $configFile;
            $configFileInfo = pathinfo($configFilePath);
            if ($configFileInfo['extension'] === 'php') {
                $tableName = $configFileInfo['filename'];
                $tablesConfig = require_once $configFilePath;
                // Vérifie si la table existe déjà
                $rows = $app->getDataBase()->query("SHOW TABLES", 'stdClass');
                $tables = array_map(function($row) {
                    return $row->Tables_in_mvc6_tablecreator;
                }, $rows);

                if (in_array($tableName, $tables)) {
                    $query = "DROP TABLE " . $tableName ;
                    $app->getDatabase()->prepareInsert($query, array());
                    continue;
                }

                //déclenche le foreach de chaque table.
                foreach ($tablesConfig['tableconfig'] as $tableInfo) {
                    $columnDefinitions = array();
                    foreach ($tableInfo as $columnName => $columnInfo) {
                        $columnType = $columnInfo['type'];
                        $columnDefault = $columnInfo['default'] !== '' ? "DEFAULT '{$columnInfo['default']}'" : '';
                        $columnAutoIncrement = $columnInfo['auto_increment'] ? 'AUTO_INCREMENT' : '';
                        $columnDefinitions[] = "$columnName $columnType $columnDefault $columnAutoIncrement";
                        if ($columnAutoIncrement) {
                            $columnDefinitions[] = "PRIMARY KEY ($columnName)";
                        }
                    }
                    //insert BDD
                    $query = "CREATE TABLE $tableName (".implode(",", $columnDefinitions).")";
                    $app->getDatabase()->prepareInsert($query, array());
                    echo self::COLORS['GREEN'] . "La table " . self::COLORS['CYAN'] . $tableName . self::COLORS['GREEN'] . " a été mise à jour avec succès.". self::COLORS['RESET'] . "\n";
                }
            }
        }
    }

    private function deleteTable(){
        $app = \Core\App::getInstance();
        echo self::COLORS['YELLOW'] . "Êtes-vous sûr de vouloir supprimer toutes les tables ? (oui/non) : " . self::COLORS['RESET'];
        $confirmation = trim(fgets(STDIN));
        if (strtolower($confirmation) !== 'oui') {
            echo self::COLORS['RED'] . "Opération annulée.\n" . self::COLORS['RESET'];
            return;
        }
        //Vérifie et selectionne les fichiers php.
        $configDir = 'config/tables';
        $configFiles = array_diff(scandir($configDir), array('..', '.'));
        foreach ($configFiles as $configFile) {
            $configFilePath = $configDir . '/' . $configFile;
            $configFileInfo = pathinfo($configFilePath);
            if ($configFileInfo['extension'] === 'php') {
                $tableName = $configFileInfo['filename'];
                $tablesConfig = require_once $configFilePath;

                // Vérifie si la table existe déjà
                $rows = $app->getDataBase()->query("SHOW TABLES", 'stdClass');
                $tables = array_map(function($row) {
                    return $row->Tables_in_mvc6_tablecreator;
                }, $rows);

                if (in_array($tableName, $tables)) {
                    $query = "DROP TABLE " . $tableName ;
                    $app->getDatabase()->prepareInsert($query, array());
                    echo self::COLORS['GREEN'] . "La table " . self::COLORS['CYAN'] . $tableName . self::COLORS['GREEN'] . " a été supprimée.". self::COLORS['RESET'] . "\n";
                }

            }
        }
    }

    private function showTable(): void
    {
        $app = App::getInstance();

        if (file_exists('config/config.php')) {
            usleep(100000);

            // Demander le nom de la table à afficher
            echo self::COLORS['YELLOW'] . "Nom de la table ('all' pour toutes les tables): " . self::COLORS['RESET'] . "\n";
            $table_name = trim(fgets(STDIN));

            if ($table_name == 'all') {
                // Récupérer toutes les tables de la base de données
                $rows = $app->getDataBase()->query("SHOW TABLES", 'stdClass');
                $tables = array_map(function($row) {
                    return $row->Tables_in_mvc6_tablecreator;
                }, $rows);

                // Afficher toutes les tables de la base de données
                echo self::COLORS['CYAN'] . "Tables de la base de données :\n" . self::COLORS['RESET'];
                foreach ($tables as $table) {

                    // Afficher la structure de la table
                    echo self::COLORS['GREEN'] . "Structure de la table " . self::COLORS['CYAN'] . $table . "\n" . self::COLORS['RESET'];
                    $rows = $app->getDataBase()->query("DESCRIBE `$table`", 'stdClass');
                    printf("%-15s %-15s %-15s %-15s %-15s %-15s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
                    foreach ($rows as $row) {
                        printf("%-15s %-15s %-15s %-15s %-15s %-15s\n", $row->Field, $row->Type, $row->Null, $row->Key, $row->Default, $row->Extra);
                    }

                }
            } else {
                if (empty($table_name)) {
                    echo self::COLORS['RED'] . "Nom de la table invalide" . self::COLORS['RESET'] . "\n";
                    return;
                }

                // Récupérer la structure de la table à partir de la base de données
                $rows = $app->getDataBase()->query("DESCRIBE `$table_name`", 'stdClass');

                // Afficher la structure de la table
                echo self::COLORS['CYAN'] . "Structure de la table `$table_name` :\n" . self::COLORS['RESET'];
                printf("%-15s %-15s %-15s %-15s %-15s %-15s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
                foreach ($rows as $row) {
                    printf("%-15s %-15s %-15s %-15s %-15s %-15s\n", $row->Field, $row->Type, $row->Null, $row->Key, $row->Default, $row->Extra);
                }
            }
        }
    }


    private function AddFixtures(){
        $app = \Core\App::getInstance();
        //Vérifie et selectionne les fichiers php.
        $configDir = 'config/tablesfixtures';
        $configFiles = array_diff(scandir($configDir), array('..', '.'));
        foreach ($configFiles as $configFile) {
            $configFilePath = $configDir . '/' . $configFile;
            $configFileInfo = pathinfo($configFilePath);
            if ($configFileInfo['extension'] === 'php') {
                $tableName = $configFileInfo['filename'];
                $tablesConfig = require_once $configFilePath;
                // Vérifie si la table existe déjà
                $rows = $app->getDataBase()->query("SHOW TABLES", 'stdClass');
                $tables = array_map(function($row) {
                    return $row->Tables_in_mvc6_tablecreator;
                }, $rows);

                if (in_array($tableName, $tables)) {
                    foreach ($tablesConfig['table'] as $tableInfo) {
                        foreach ($tableInfo as $Info) {
                            $columnNames = array();
                            $columnData = array();
                            foreach ($Info as $columnInfo) {
                                $columnNames[] = $columnInfo['name'];
                                $columnData[] = $columnInfo['data'];
                            }
                            $columnNamesString = implode(', ', $columnNames);
                            $columnDataPlaceholders = implode(', ', array_fill(0, count($columnData), '?'));

                            // Create the INSERT query dynamically using the column names and data
                            $query = "INSERT INTO " . $tableName . " ( " . $columnNamesString . ") VALUES (" . $columnDataPlaceholders . ")";

                            // Execute the query with the data
                            $app->getDatabase()->prepareInsert($query, $columnData);


                        }
                    }
                    echo self::COLORS['GREEN'] . "Les 'fixtures' on été ajouté à la table " . self::COLORS['CYAN'] . $tableName . self::COLORS['GREEN'] . self::COLORS['RESET'] . "\n";

                }







            }
        }
    }
}

