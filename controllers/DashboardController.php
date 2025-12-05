<?php
require_once 'models/Evidencia.php';
require_once 'models/Institucion.php';
require_once 'models/Ciclo.php';
require_once 'models/Usuario.php';

class DashboardController {
    private $db;
    private $evidencia;
    private $institucion;
    private $ciclo;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->evidencia = new Evidencia($this->db);
        $this->institucion = new Institucion($this->db);
        $this->ciclo = new Ciclo($this->db);
        $this->usuario = new Usuario($this->db);
    }

    public function index() {
        $this->checkSession();
        
        // Estadísticas para el dashboard
        $estadisticas = $this->getEstadisticas();
        
        // Obtener datos para las vistas
        $evidencias_recientes = $this->getEvidenciasRecientes();
        $evidencias_por_ciclo = $this->getEvidenciasPorCiclo();
        $instituciones_activas = $this->getInstitucionesActivas();
        
        // Definir variables para la vista
        $page_title = "Dashboard";
        
        // Incluir vistas
        include 'includes/header.php';
        // include 'includes/sidebar.php';
        include 'views/dashboard/index.php';
        include 'includes/footer.php';
    }

    private function getEstadisticas() {
        return [
            'total_instituciones' => $this->getTotalInstituciones(),
            'total_evidencias' => $this->getTotalEvidencias(),
            'total_ciclos' => $this->getTotalCiclos(),
            'total_usuarios' => $this->getTotalUsuarios(),
            'evidencias_hoy' => $this->getEvidenciasHoy(),
            'evidencias_mes' => $this->getEvidenciasMes()
        ];
    }

    private function getTotalInstituciones() {
        $query = "SELECT COUNT(*) as total FROM instituciones WHERE activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getTotalEvidencias() {
        $query = "SELECT COUNT(*) as total FROM evidencias";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getTotalCiclos() {
        $query = "SELECT COUNT(*) as total FROM ciclos WHERE activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getTotalUsuarios() {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getEvidenciasHoy() {
        $query = "SELECT COUNT(*) as total FROM evidencias WHERE DATE(fecha) = CURDATE()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getEvidenciasMes() {
        $query = "SELECT COUNT(*) as total FROM evidencias 
                  WHERE YEAR(fecha) = YEAR(CURDATE()) 
                  AND MONTH(fecha) = MONTH(CURDATE())";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getEvidenciasRecientes() {
        try {
            $query = "SELECT e.*, i.nombre as institucion_nombre, 
                             c.descripcion as ciclo_descripcion,
                             u.nombre_completo as usuario_nombre
                      FROM evidencias e
                      LEFT JOIN instituciones i ON e.institucion_id = i.id
                      LEFT JOIN ciclos c ON e.ciclo_id = c.id
                      LEFT JOIN usuarios u ON e.usuario_id = u.id
                      ORDER BY e.created_at DESC
                      LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            // Si hay error, devolver un resultado vacío
            return new class {
                public function rowCount() { return 0; }
                public function fetch($mode = PDO::FETCH_ASSOC) { return false; }
            };
        }
    }

    private function getEvidenciasPorCiclo() {
        try {
            $query = "SELECT c.descripcion, COUNT(e.id) as total
                      FROM ciclos c
                      LEFT JOIN evidencias e ON c.id = e.ciclo_id
                      WHERE c.activo = 1
                      GROUP BY c.id
                      ORDER BY c.descripcion DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            // Si hay error, devolver un resultado vacío
            return new class {
                public function rowCount() { return 0; }
                public function fetch($mode = PDO::FETCH_ASSOC) { return false; }
            };
        }
    }

    private function getInstitucionesActivas() {
        try {
            $query = "SELECT i.nombre, i.ciudad, COUNT(e.id) as total_evidencias
                      FROM instituciones i
                      LEFT JOIN evidencias e ON i.id = e.institucion_id
                      WHERE i.activo = 1
                      GROUP BY i.id
                      ORDER BY total_evidencias DESC
                      LIMIT 5";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            // Si hay error, devolver un resultado vacío
            return new class {
                public function rowCount() { return 0; }
                public function fetch($mode = PDO::FETCH_ASSOC) { return false; }
            };
        }
    }

    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }
}
?>