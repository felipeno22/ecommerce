<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class OrderStatus extends Model {

	const EM_ABERTO = 5;
	const AGUARDANDO_PAGAMENTO = 6;
	const PAGO=7;
	const ENTREGUE=8;

}
?>