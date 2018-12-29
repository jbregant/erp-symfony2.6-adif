<?php

namespace ADIF\AutenticacionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Gustavo Luis
 * @date 10/08/2017
 */
class UsuarioRepository extends EntityRepository
{

	public function getRolesByIdUsuarioAndIdEmpresa($idUsuario, $idEmpresa)
	{
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('roles', 'roles');

		$sql = "
			SELECT g.roles
			FROM usuario u
			INNER JOIN usuario_grupo_empresa uge ON u.id = uge.id_usuario
			INNER JOIN grupo g ON uge.id_grupo = g.id
			WHERE u.id = :idUsuario
			AND uge.id_empresa = :idEmpresa
			AND u.enabled IS TRUE
		";

		$query = $this->_em->createNativeQuery($sql, $rsm);

		$query->setParameter('idUsuario', $idUsuario);
        $query->setParameter('idEmpresa', $idEmpresa);

		return $query->getResult();
	}

	public function getGruposByIdUsuarioAndIdEmpresa($idUsuario, $idEmpresa)
	{
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('id_grupo', 'id_grupo');
        $rsm->addScalarResult('grupo', 'grupo');

		$sql = "
			SELECT g.id AS id_grupo, g.name as grupo
			FROM usuario u
			INNER JOIN usuario_grupo_empresa uge ON u.id = uge.id_usuario
			INNER JOIN grupo g ON uge.id_grupo = g.id
			WHERE u.id = :idUsuario
			AND uge.id_empresa = :idEmpresa
			AND u.enabled IS TRUE
		";

		$query = $this->_em->createNativeQuery($sql, $rsm);

		$query->setParameter('idUsuario', $idUsuario);
        $query->setParameter('idEmpresa', $idEmpresa);

		return $query->getResult();
	}

	public function updateUsuario(Request $request, $id)
	{
		$nombre = $request->get('adif_autenticacionbundle_usuario')['nombre'];
		$apellido = $request->get('adif_autenticacionbundle_usuario')['apellido'];
		$email	= $request->get('adif_autenticacionbundle_usuario')['email'];
		$username =	$request->get('adif_autenticacionbundle_usuario')['username'];
		$idArea = $request->get('adif_autenticacionbundle_usuario')['area'];
		$enabled = isset($request->get('adif_autenticacionbundle_usuario')['enabled']) ? 1 : 0;

		try {

			$this->_em->getConnection()->beginTransaction();

			$sql = "
				UPDATE usuario
				SET nombre = '$nombre',
				apellido = '$apellido',
				email = '$email',
				username = '$username',
				id_area = '$idArea',
				enabled = '$enabled'
				WHERE id = $id
			";

			$stmt = $this->_em->getConnection()->prepare($sql);
			$stmt->execute();

			$this->_em->getConnection()->commit();

		} catch(\Exception $e) {
			$this->_em->getConnection()->rollback();
			throw new \Exception($e);
		}
	}

	public function addGruposByIdUsuarioAndIdEmpresa($idUsuario, $idEmpresa, array $idsGrupos = array())
	{
		try {

			$this->_em->getConnection()->beginTransaction();

			$sql = "
				DELETE FROM usuario_grupo_empresa
				WHERE 1 = 1
				AND id_usuario = $idUsuario
				AND id_empresa = $idEmpresa
			";

			$stmt = $this->_em->getConnection()->prepare($sql);
			$stmt->execute();

			foreach($idsGrupos as $idGrupo) {

				$sql = "
					INSERT INTO usuario_grupo_empresa (id_usuario, id_grupo, id_empresa)
					VALUES($idUsuario, $idGrupo, $idEmpresa)
				";

				$stmt = $this->_em->getConnection()->prepare($sql);
				$stmt->execute();
			}

			$this->_em->getConnection()->commit();

		} catch(\Exception $e) {
			$this->_em->getConnection()->rollback();
			throw new \Exception($e);
		}

	}

	public function addGruposByIdUsuarioAndIdEmpresaOnCreate($idUsuario, $idEmpresa, array $idsGrupos = array())
	{
		try {

			$this->_em->getConnection()->beginTransaction();

			$sql = "
				DELETE FROM usuario_grupo_empresa
				WHERE 1 = 1
				AND id_usuario = $idUsuario
			";

			$stmt = $this->_em->getConnection()->prepare($sql);
			$stmt->execute();

			foreach($idsGrupos as $idGrupo) {

				$sql = "
					INSERT INTO usuario_grupo_empresa (id_usuario, id_grupo, id_empresa)
					VALUES($idUsuario, $idGrupo, $idEmpresa)
				";

				$stmt = $this->_em->getConnection()->prepare($sql);
				$stmt->execute();
			}

			$this->_em->getConnection()->commit();

		} catch(\Exception $e) {
			$this->_em->getConnection()->rollback();
			throw new \Exception($e);
		}

	}

}
