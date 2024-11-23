<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m210611_161713_add_locations2 extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->truncateTable('event_location_entrances');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

//        $this->insert('event_location', ['id' => 1,'name' => "Palazzo lombardia, Milano", 'hidden' => false, 	'event_place_id' => 'ChIJnxcNEdLGhkcR1Wd9ntrUgco',	'created_at' => '2020-03-02 18:36:47', 'updated_at' => '2020-03-03 09:24:59', 'deleted_at' => NULL, 'created_by' =>1, 'updated_by' => 1, 'deleted_by' => null]);
//        $this->insert('event_location', ['id' => 5,'name' => "Palazzo Pirelli, Milano", 'hidden' => false, 	'event_place_id' => 'ChIJiyEi7s_GhkcRWWyTagHUZAw',	'created_at' => '2020-03-02 18:36:47', 'updated_at' => '2020-03-03 09:24:59', 'deleted_at' => NULL, 'created_by' =>1, 'updated_by' => 1, 'deleted_by' => null]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Auditorium Testori - Piazza Città di Lombardia - ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Foyer Testori - Piazza Città di Lombardia - ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Piazza Città di Lombardia", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "IsolaSet Spazio Esposizioni Temporanee - ingresso Piazza Città di Lombardia", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Sala M. Biagi - Via M. Gioia, 37 - ingresso N4", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Spazio Fiume - Via M. Gioia, 37 - ingresso N4", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Sala Solesin - Via M. Gioia, 37 - ingresso N4", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "SpazioPhoto - Via Galvani, 27 - ingresso N2", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Sala Falcone – 11° piano	Piazza Città di Lombardia	ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Sala opportunità – 13° piano - Piazza Città di Lombardia	 - ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Belvedere 39° piano - Piazza Città di Lombardia - ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "Gli uffici del Presidente - ingresso N1", ]);
        $this->insert('event_location_entrances',['event_location_id' => 1, 'name' => "La Terrazza - ingresso N1", ]);


        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Auditorium Gaber - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Consiliare - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Gio Ponti - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Foyer Gio Ponti - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Spazio Eventi - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Foyer Piazza duca d'aosta - Fabio Filzi", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Gonfalone - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Pirelli - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Piano della memoria - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "Belvedere Enzo Jannacci - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "La Madonnina - Fabio Filzi, 22", ]);
        $this->insert('event_location_entrances',['event_location_id' => 5, 'name' => "I Falchi pellegrini - Fabio Filzi, 22", ]);

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Auditorium Testori - Piazza Città di Lombardia - ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Foyer Testori - Piazza Città di Lombardia - ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Piazza Città di Lombardia", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "IsolaSet Spazio Esposizioni Temporanee - ingresso Piazza Città di Lombardia", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Sala M. Biagi - Via M. Gioia, 37 - ingresso N4", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Spazio Fiume - Via M. Gioia, 37 - ingresso N4", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Sala Solesin - Via M. Gioia, 37 - ingresso N4", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "SpazioPhoto - Via Galvani, 27 - ingresso N2", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Sala Falcone – 11° piano	Piazza Città di Lombardia	ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Sala opportunità – 13° piano	- Piazza Città di Lombardia	 - ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Belvedere 39° piano - Piazza Città di Lombardia - ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "Gli uffici del Presidente - ingresso N1", ]);
        $this->delete('event_location_entrances',['event_location_id' => 1, 'name' => "La Terrazza - ingresso N1", ]);


        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Auditorium Gaber - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Consiliare - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Gio Ponti - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Foyer Gio Ponti - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Spazio Eventi - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Foyer Piazza duca d'aosta - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Gonfalone - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Sala Pirelli - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Piano della memoria - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "Belvedere Enzo Jannacci - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "La Madonnina - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);
        $this->delete('event_location_entrances',['event_location_id' => 5, 'name' => "I Falchi pellegrini - Fabio Filzi, 22/Piazza Duca d'Aosta, 3", ]);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
