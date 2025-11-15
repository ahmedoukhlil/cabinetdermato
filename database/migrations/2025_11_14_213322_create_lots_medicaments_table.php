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
        Schema::create('lots_medicaments', function (Blueprint $table) {
            $table->increments('idLot');
            $table->unsignedInteger('fkidStock')->index('idx_stock');
            $table->unsignedInteger('fkidMedicament')->index('idx_medicament');
            $table->string('numeroLot', 100)->nullable()->comment('Numéro de lot fournisseur');
            $table->double('quantiteInitiale')->default(0)->comment('Quantité initiale du lot');
            $table->double('quantiteRestante')->default(0)->comment('Quantité restante dans le lot');
            $table->date('dateExpiration')->nullable()->index('idx_date_expiration')->comment('Date d\'expiration du lot');
            $table->dateTime('dateEntree')->nullable()->comment('Date d\'entrée du lot');
            $table->double('prixAchatUnitaire')->default(0)->comment('Prix d\'achat unitaire du lot');
            $table->string('fournisseur', 255)->nullable()->comment('Nom du fournisseur');
            $table->string('referenceFacture', 100)->nullable()->comment('Référence facture fournisseur');
            $table->unsignedInteger('fkidUser')->default(1)->comment('Utilisateur qui a créé le lot');
            $table->unsignedInteger('Masquer')->default(0)->comment('0=Actif, 1=Masqué');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lots_medicaments');
    }
};
