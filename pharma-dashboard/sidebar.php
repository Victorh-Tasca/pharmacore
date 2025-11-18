<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="images/logop.png" alt="Logo PharmaKore" class="sidebar-logo">
        <h3>PharmaKore</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="index.html">
                <i class="bi bi-grid-fill"></i> DashBoard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="estoque/estoque.html">
                <i class="bi bi-box-seam-fill"></i> Estoque (Lotes)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="estoque/estoque_medicamento.html">
                <i class="bi bi-clipboard-data-fill"></i> Estoque (Medicamentos)
            </a>
        </li>
        <hr class="sidebar-divider my-2">

        <li class="nav-item">
            <a class="nav-link collapsed dropdown-toggle" data-bs-toggle="collapse" href="#collapseCadastros" aria-expanded="false" aria-controls="collapseCadastros">
                <i class="bi bi-folder-fill"></i> CADASTROS
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
        </li>
        <div class="collapse" id="collapseCadastros" data-bs-parent="#sidebar">
            <ul class="nav flex-column ps-3"> 
                <li class="nav-item">
                    <a class="nav-link" href="cadastro/medicamentos.html">
                        <i class="bi bi-plus-circle-fill"></i> Medicamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastro/pacientes.html">
                        <i class="bi bi-person-fill"></i> Pacientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastro/laboratorios.html">
                        <i class="bi bi-building"></i> Laboratórios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastro/classes_terapeuticas.html">
                        <i class="bi bi-tags-fill"></i> Classes Terapêuticas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastro/fornecedores.html">
                        <i class="bi bi-truck"></i> Fornecedores
                    </a>
                </li>
            </ul>
        </div>
        <hr class="sidebar-divider my-2">

        <li class="nav-item">
            <a class="nav-link collapsed dropdown-toggle" data-bs-toggle="collapse" href="#collapseRelatorios" aria-expanded="false" aria-controls="collapseRelatorios">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> RELATÓRIOS
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
        </li>
        <div class="collapse" id="collapseRelatorios" data-bs-parent="#sidebar">
            <ul class="nav flex-column ps-3"> 
                <li class="nav-item">
                    <a class="nav-link" href="relatorios/alertas.html">
                        <i class="bi bi-exclamation-triangle-fill"></i> Alertas de Validade
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="relatorios/relatorios.html">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Relatórios
                    </a>
                </li>
            </ul>
        </div>
        <hr class="sidebar-divider my-2">

        <li class="nav-item">
            <a class="nav-link collapsed dropdown-toggle" data-bs-toggle="collapse" href="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                <i class="bi bi-gear-fill"></i> ADMINISTRAÇÃO
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
        </li>
        <div class="collapse" id="collapseAdmin" data-bs-parent="#sidebar">
            <ul class="nav flex-column ps-3"> 
                <li class="nav-item">
                    <a class="nav-link" href="admin/usuarios.html">
                        <i class="bi bi-people-fill"></i> Usuários
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/papeis.html">
                        <i class="bi bi-shield-lock-fill"></i> Papéis e Permissões
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/configuracoes.html">
                        <i class="bi bi-gear-fill"></i> Configurações
                    </a>
                </li>
            </ul>
        </div>
    </ul>
</nav>