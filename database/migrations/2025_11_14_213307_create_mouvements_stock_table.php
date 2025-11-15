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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->increments('idMouvement');
            $table->unsignedInteger('fkidStock')->index('idx_stock');
            $table->unsignedInteger('fkidMedicament')->index('idx_medicament');
            $table->unsignedInteger('fkidLot')->nullable()->index('idx_lot')->comment('Référence au lot si applicable');
            $table->enum('typeMouvement', ['ENTREE', 'SORTIE', 'AJUSTEMENT'])->index('idx_type');
            $table->double('quantite')->comment('Quantité du mouvement (positive pour entrée, négative pour sortie)');
            $table->double('prixUnitaire')->default(0)->comment('Prix unitaire au moment du mouvement');
            $table->double('montantTotal')->default(0)->comment('Montant total du mouvement');
            $table->string('motif', 255)->nullable()->comment('Raison du mouvement');
            $table->unsignedInteger('fkidFacture')->nullable()->index('idx_facture')->comment('Si sortie liée à une vente');
            $table->unsignedInteger('fkidDetailFacture')->nullable()->index('idx_detail_facture')->comment('Référence au détail de facture');
            $table->unsignedInteger('fkidPatient')->nullable()->index('idx_patient')->comment('Patient concerné si vente');
            $table->unsignedInteger('fkidUser')->index('idx_user')->comment('Utilisateur qui a effectué le mouvement');
            $table->dateTime('dateMouvement')->nullable()->index('idx_date');
            $table->string('reference', 100)->nullable()->comment('N° facture fournisseur, N° facture vente, etc.');
            $table->text('notes')->nullable()->comment('Notes additionnelles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
