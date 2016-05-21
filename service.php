<?php

/**
 * Service ESTADO
 * 
 * @author kuma
 * @version 1.0
 */
class Estado extends Service
{
	/**
	 * Main behavior
	 *
	 * @author kuma
	 * @param Request $request
	 * @return Response
	 *
	 */
	public function _main(Request $request)
	{
		$response = new Response();
		$response->createFromText('Este es un servicio de administracin y debe ser usado con parametros extras. Si no conoce los parametros, pregunte a su administrador.');
		return $response;		
	}

	/**
	 * Show promotion statistics
	 *
	 * @author kuma
	 * @param Request $request
	 * @return Response
	 *
	 */
	public function _promotor($request)
	{
		// clear query string
		$source = trim(strtolower($request->query));

		// empty query/email address
		if (trim($source) == '')
		{
			$response = new Response();
			$response->createFromText('Especifique el email desde el cual promocionas despu&eacute;s de la frase <b>ESTADO PROMOTOR</b> en el asunto.');
			return $response;
		}

		// connect to db
		$db = new Connection();

		// get stats from db
		$sql = "SELECT 'TOTAL' as stat, count(*) as val FROM first_timers WHERE source = '$source' UNION SELECT 'PAID' as stat, count(*) as val FROM first_timers WHERE paid = 1 AND source = '$source';";		
		$r = $db->deepQuery($sql);

		// send response
		if ($r !== false)
		{
			$stats = array();
			foreach ($r as $item)
			{
				$stats[$item->stat] = $item->val;
			}

			$response = new Response();
			$response->setResponseSubject("Estado de la promocion de $source");
			$response->createFromTemplate('promotor.tpl', array(
				'total' => $stats['TOTAL'],
				'paid' => $stats['PAID'],
				'source' => $source
			));

			return $response;
		}

		$response = new Response();
		$response->setResponseSubject('No se pudo obtener las estadisticas');
		$response->createFromText('En estos momentos no se pudo obtener las estad&iacute;sticas que solicitaste;. Por favor, intent&eacute;ntalo m&aacute;s tarde o contacta con el soporte t&eacute;cnico.');
		return $response;
	}
}