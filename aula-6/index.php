<!DOCTYPE html>
<!-- ng-app -> definindo as fronteiras da aplicação -->
<html lang="pt-BR" ng-app="listaTelefonica">
    <head>
	    <meta charset="UTF-8">
        <title>Lista Telefônica</title>
        <link rel="stylesheet" type="text/css" href="/node_modules/bootstrap/dist/css/bootstrap.css">
        <style>
            .jumbotron {
                width: 600px;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
                margin-top: 20px;
				padding: 48px;
            }

            .selecionado{
                background-color: yellow;
            }

            .negrito{
                font-weight: bold;
            }
            input, select, button{
                margin-bottom: 10px;
            }
			a {
				cursor: pointer;
			}
        </style>
        <script src="/node_modules/angular/angular.js"></script>
        <script src="/node_modules/angular/angular-messages.js"></script>
        <script src="/node_modules/angular/angular-locale_pt-br.js"></script>
        <script>
            angular.module("listaTelefonica", ["ngMessages"]);
            angular.module("listaTelefonica").controller("listaTelefonicaCtrl", 
                function ($scope, $filter, $http) {
                    $scope.app = "Lista Telefônica";

                    $scope.contatos = [
                        { nome: "Pedro", telefone: "99998888", data: new Date(), operadora: { nome: "Oi", codigo: 14, categoria: "Celular" }, cor:"blue" },
                        { nome: "Ana", telefone: "99998877", data: new Date(), operadora: { nome: "Vivo", codigo: 15, categoria: "Celular" }, cor:"red" },
                        { nome: "Maria", telefone: "99998866", data: new Date(), operadora: { nome: "Tim", codigo: 41, categoria: "Celular" }, cor:"yellow" }
                    ];

                    $scope.operadoras = [
                        { nome: "Oi", codigo: 14, categoria: "Celular", preco: 2 },
						{ nome: "Vivo", codigo: 15, categoria: "Celular", preco: 1 },
						{ nome: "Tim", codigo: 41, categoria: "Celular", preco: 3 },
						{ nome: "GVT", codigo: 25, categoria: "Fixo", preco: 1 },
						{ nome: "Embratel", codigo: 21, categoria: "Fixo", preco: 2 }
                    ];
					
					$scope.getRandomColor = function() {
						const letters = '0123456789ABCDEF';
						let color = '#';
						for (let i = 0; i < 6; i++) {
							color += letters[Math.floor(Math.random() * 16)];
						}
						return color;
					};
                    
                    $scope.adicionarContato = function (contato) {
						contato.data = new Date();
						contato.cor = $scope.getRandomColor();

                        $scope.contatos.push(contato);
                        delete $scope.contato;
                        $scope.contatoForm.$setPristine();
                    };

                    $scope.apagarContatos = function (contatos) {
                        $scope.contatos = this.contatos.filter(function (contato) {
                            if (!contato.selecionado) return contato;
                        });
                    };

                    $scope.isContatoSelecionado = function (contatos) {
                        return contatos.some(function (contato) {
                            return contato.selecionado;
                        });
                    };

					$scope.ordenarPor = function(campo){
						$scope.criterioDeOrdenacao = campo;
						$scope.direcaoDaOrdenacao = !$scope.direcaoDaOrdenacao;
					}
                }
            );
        </script>
    </head>
    <!-- ng-controller -> Vinculando um elemento da View ao Controller -->
    <body ng-controller="listaTelefonicaCtrl">
        <div class="jumbotron">
            <!-- ng-bind -> Substituindo o elemento por uma expressão -->
            <h3 ng-bind="app"></h3>
			<input class="form-control" type="text" ng-model="criterioDeBusca" placeholder="O que você está buscando?">
            <table ng-show="contatos.length > 0" class="table table-striped">
                <thead>
                    <th></th>
                    <th><a ng-click="ordenarPor('nome')">Nome</a></th>
                    <th><a ng-click="ordenarPor('telefone')">Telefone</a></th>
                    <th>Operadora</th>
                    <th>Data</th>
                    <th></th>
                </thead>
                <tbody>
                    <!-- ng-repeat -> Iterando sobre os itens de uma coleção ou de um objeto -->
                    <tr ng-class="{'selecionado negrito':contato.selecionado}" ng-repeat="contato in contatos | filter:criterioDeBusca | orderBy: criterioDeOrdenacao:direcaoDaOrdenacao">
                        <td><input type="checkbox" ng-model="contato.selecionado" style="cursor: pointer;"></td>
                        <td ng-bind="contato.nome"></td>
                        <td ng-bind="contato.telefone"></td>
                        <td ng-bind="contato.operadora.nome"></td>
                        <td ng-bind="contato.data | date:'dd/MM/yyyy HH:mm'"></td>
                        <td><div style="width: 20px; height: 20px;" ng-style="{'background-color': contato.cor}"></div></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <form name="contatoForm">
                <!-- ng-model -> Vinculando um elemento de formulário a uma propriedade do $scope -->
                <input class="form-control" type="text" ng-model="contato.nome" name="nome" placeholder="Nome" ng-required="true" ng-minlength="10">
                <input class="form-control" type="text" ng-model="contato.telefone" name="telefone" placeholder="Telefone" ng-required="true" ng-pattern="/^\d{5}-\d{4}$/">
                <!-- ng-options -> Especificando as opções de um elemento select -->
                <select class="form-control" ng-model="contato.operadora" ng-options="operadora.nome + ' (' + (operadora.preco | currency) + ') '  group by operadora.categoria for operadora in operadoras | orderBy: 'nome'">
                    <option value="">Selecione uma operadora</option>
                </select>
            </form>
			<div ng-show="contatoForm.nome.$dirty" ng-messages="$contatoForm.nome.$error">
				<div ng-message="required" class="alert alert-danger">
					Por favor, preencha o campo nome!
				</div>
				<div ng-message="minlength" class="alert alert-danger">
					O campo nome deve ter no mínimo 10 caracteres.
				</div>
			</div>
			<div ng-show="contatoForm.telefone.$dirty" ng-messages="$contatoForm.telefone.$error">
				<div ng-message="required" class="alert alert-danger">
					Por favor, preencha o campo telefone!
				</div>
				<div ng-message="pattern" class="alert alert-danger">
				   O campo telefone dever estar no formato DDDDD-DDDD.
				</div>
			</div>
            <!-- ng-click -> Associando um evento de clique a um elemento -->
            <!-- ng-disabled -> Desabilitando um elemento -->
            <button class="btn btn-primary btn-block" ng-click="adicionarContato(contato)" ng-disabled="contatoForm.$invalid">Adicionar Contato</button>
            <button class="btn btn-danger btn-block" ng-click="apagarContatos(contato)" ng-if="isContatoSelecionado(contatos)">Apagar Contato</button>
        </div>
        <div ng-include="'footer.php'"></div>
    </body>
</html>