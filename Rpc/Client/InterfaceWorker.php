<?php
/**
 * Your file description.
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 12:01
 */

namespace Rpc;


Interface InterfaceWorker
{
	/**
	 * 数据封装
	 *
	 * @param $data
	 * @return mixed
	 */
	public function pack($data);

	/**
	 * 数据解封
	 *
	 * @param $data
	 * @return mixed
	 */
	public function unpack($data);
}