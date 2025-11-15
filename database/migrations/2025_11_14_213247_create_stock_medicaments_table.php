<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_medicaments', function (Blueprint $table) {
            $table->increments('idStock');
            $table->unsignedInteger('fkidMedicament')->index('idx_medicament');
            $table->unsignedInteger('fkidCabinet')->index('idx_cabinet');
            $table->double('quantiteStock')->default(0)->comment('Quantité totale en stock');
            $table->double('quantiteMin')->default(0)->comment('Seuil minimum d\'alerte');
            $table->double('prixAchat')->default(0)->comment('Prix d\'achat moyen');
            $table->double('prixVente')->default(0)->comment('Prix de vente (peut venir de medicaments.PrixRef)');
            $table->dateTime('dateDerniereEntree')->nullable();
            $table->dateTime('dateDerniereSortie')->nullable();
            $table->unsignedInteger('Masquer')->default(0)->comment('0=Actif, 1=Masqué');
            
            // Contrainte unique : un médicament ne peut avoir qu'un seul stock par cabinet
            $table->unique(['fkidMedicament', 'fkidCabinet'], 'unique_medicament_cabinet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_medicaments');
    }
};
