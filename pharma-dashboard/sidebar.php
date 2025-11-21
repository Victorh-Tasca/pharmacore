<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="/pharmacore/pharma-dashboard/images/logop.png" alt="Logo PharmaKore" class="sidebar-logo">
        <h3>PharmaKore</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="/pharmacore/pharma-dashboard/index.html">
                <i class="bi bi-grid-fill"></i> DashBoard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed dropdown-toggle" data-bs-toggle="collapse" href="#collapseEstoque" aria-expanded="false" aria-controls="collapseEstoque">
                <i class="bi bi-box-seam-fill"></i> ESTOQUE
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
        </li>
        
        <div class="collapse" id="collapseEstoque" data-bs-parent="#sidebar">
            <ul class="nav flex-column ps-3"> 
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/estoque/estoque.html">
                        <i class="bi bi-box-seam-fill"></i> Lotes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/estoque/estoque_medicamento.html">
                        <i class="bi bi-clipboard-data-fill"></i> Medicamentos
                    </a>
                </li>
            </ul>
        </div>
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
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/cadastro/medicamentos.html">
                        <i class="bi bi-plus-circle-fill"></i> Medicamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/cadastro/pacientes.html">
                        <i class="bi bi-person-fill"></i> Pacientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/cadastro/laboratorios.html">
                        <i class="bi bi-building"></i> Laboratórios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/cadastro/classes_terapeuticas.html">
                        <i class="bi bi-tags-fill"></i> Classes Terapêuticas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/cadastro/fornecedores.html">
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
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/relatorios/alertas.html">
                        <i class="bi bi-exclamation-triangle-fill"></i> Alertas de Validade
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/relatorios/relatorios.html">
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
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/admin/usuarios.html">
                        <i class="bi bi-people-fill"></i> Usuários
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/admin/papeis.html">
                        <i class="bi bi-shield-lock-fill"></i> Papéis e Permissões
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pharmacore/pharma-dashboard/admin/configuracoes.html">
                        <i class="bi bi-gear-fill"></i> Configurações
                    </a>
                </li>
            </ul>
        </div>
    </ul>
</nav>