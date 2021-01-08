@extends('layouts.app')

@section('title', 'Home')

@section('content')

@include('components.drawer')

<section class="app-container" id="app">

    <div class="container">

        <div class="row">

            <div class="col-md-4">
                <div class="row mb-2">
                    <div class="col-12 font-size-8 mb-4">
                        <h4 class="font-weight-bolder font-italic font-roxo">1º Registre uma sessão</h4>
                        <img src="assets/images/registre-uma-sessao.jpg" class="img-fluid mb-3" alt="Registre uma sessão" />
                        <p class="font-weight-normal">Você que profissional de saúde ou trabalha em alguma instituição de saúde pública e está enfrentando escassez de recursos hospitalares, inicie uma sessão anoninamente para começar e salve seu código de maneira a poder reutilizar.</p>
                        
                    </div>
                    <div class="col-12 font-size-8 mb-4">
                        <h4 class="font-weight-bolder font-italic font-roxo">2º Abra uma solicitação</h4>
                        <img src="assets/images/abra-solicitacao.jpg" class="img-fluid mb-3" alt="Registre uma sessão" />
                        <p class="font-weight-normal">Ao abrir uma nova solicitação você divulga a possíveis empresas ou instituições que determinado recurso está em estado crítico, com isso pode receber apoios.</p>
                        
                    </div>
                    <div class="col-12 font-size-8 mb-4">
                        <h4 class="font-weight-bolder font-italic font-roxo">3º Veja quem apoiou</h4>
                        <img src="assets/images/visualizar-apoios.jpg" class="img-fluid mb-3" alt="Registre uma sessão" />
                        <p class="font-weight-normal">Após algum tempo retorne ao site e verifique os apoios recebidos. Se sessão não carregar automaticamente, clique em "Carregar Sessão" e insira o código previamente salvo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">

                <div class="filtros row mt-4 justify-content-between">
                    <div class="col-12">
                        <ul class="list-unstyled row">
                            <li class="col-12 col-md-auto mb-2 mb-md-0">
                                <button v-on:click="registrarSessao" variant="outline-primary" size="md" class="shadow btn-block">Registrar nova sessão</button>
                            </li>
                            <li class="col-12 col-md-auto mb-2 mb-md-0">
                                <button v.modal-token variant="outline-info" size="md" class="shadow btn-block">Carregar Sessão</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 mb-2 mb-md-0">
                        Sessão Atual: 
                        <strong v-if="token">{{ 'token' }}</strong>
                        <strong v-else>Não iniciada</strong>
                    </div>
                </div>

                <h2 class="mt-5 mb-4">Solicitações Abertas</h2>

                <div v-if="mostrarBotaoSolicitacao" class="row justify-content-between mb-4">
                    
                    <div v-if="token" class="col-12 col-md-auto">
                        <button size="md" variant="primary" class="btn-block shadow float-right" 
                        v.modal-solicitacao>Abrir uma solicitação 
                        </button>
                    </div> 
                    <div v-else class="col-12 col-md-auto"></div>  
                    
                    <div class="col-12 mt-3 mt-sm-0 col-sm-auto">
                        <div class="form-group justify-content-start">
                            <div label-align-sm="left" label-cols-sm="4" label="Filtrar" id="filtro-regiao" label-for="filtro-regiao">
                                <select v-model="filtro" :options="bairros" size="md" v-on:change="filtrarBairro">
                                </select>
                            </div>
                        </div>                    
                    </div>
                    
                </div>

                <div class="row justify-content-between">                

                    <div v-if="items && items.length <= 0" class="col-12 text-center">
                        Nenhum resultado
                    </div>

                    <div class="col-md-12 pr-4 pl-4 mb-3" v-for="(item, post_index) in items" :key="post_index">

                        <div :id="'post-' + item.ID" class="row card card-post p-3 shadow-sm">

                            <div class="col-12">

                                <div variant="secondary" class="float-right" v-bind:target="'_blank'"
                                    v-bind:href="'https://www.google.com/maps?q='+item.address+','+item.number+','+item.neighborhood+','+item.city+','+item.state">
                                    Ver no Google Maps
                                </div>

                                <p><small>Postado @{{ momentFrom(item.updated_at) }}</small></p>

                                <h4>@{{ item.name }}</h4>

                                <address class="font-size-7">
                                    <p>
                                        
                                        @{{ item.address }}, @{{ item.number }}<br />
                                        @{{ item.neighborhood }}, @{{ item.city }} - @{{ item.state }}<br />
                                        CEP: @{{ item.cep }}                                         
                                    </p>
                                </address>
                                <hr />
                            </div>

                            <div class="col-12">
                                <h5>Recursos críticos</h5>
                                <div class="row">
                                    <template v-if="item.items">

                                        <div class="col mb-3" v-for="(i, index) in item.items" :key="index">

                                            <div button-group size="sm" class="m-1" v-for="(tag, subindex) in i"
                                                :key="subindex">

                                                <button class="float-left" :variant="(tag[2])? 'success' : 'light'"
                                                    size="sm">

                                                    @{{ tag[1] }}

                                                    <template v-if="tag[2]">

                                                        <div v-for="(support, supportindex) in tag[2]" :key="supportindex" size="sm"
                                                            variant="light" class="mr-1">

                                                            <span :id="`popover-${index}-${subindex}-${supportindex}`">
                                                                @{{ support['name'] }}
                                                            </span>

                                                            <b-popover v-if="support != undefined"
                                                                :target="`popover-${index}-${subindex}-${supportindex}`"
                                                                :placement="'index-'+index+'-'+subindex+'-'+supportindex"
                                                                title="Este recurso recebeu apoio!" triggers="click">
                                                                <ul>
                                                                    <li>
                                                                        @{{ support['name'] }}
                                                                    </li>
                                                                    <li>
                                                                        <a :href="'mailto:'+ support['email']">
                                                                            @{{ support['email'] }}
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a :href="support['site']" target="_blank">
                                                                            {{ 'Website' }}
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </b-popover>

                                                        </div>

                                                    </template>

                                                    <div v-else v.modal-quero-ajuda size="sm" variant="primary"
                                                        v-on:click="setarAjuda(tag)">
                                                        Apoiar
                                                    </div>

                                                </button>

                                            </div>

                                        </div>
                                    </template>
                                    
                                    
                                </div>

                                <div v-if="item.can_edit != undefined && item.can_edit == true">
                                
                                    <button size="sm" variant="outline-danger" class="ml-3 float-right" v-on:click="deletarSolicitacao(item.ID, post_index)">
                                        Deletar
                                    </button>

                                </div><!-- Botões de ações -->

                            </div>                                

                        </div><!-- Informações da Instituição -->

                    </div><!-- Solicitação -->

                    <div class="col-12 mt-4 mb-4 text-right">
                        <button 
                            variant="outline-primary" 
                            v-on:click="carregarSolicitacao">
                            Carregar Mais
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div><!-- Fltros de pesquisa de solicitações -->

    <div id="modal-solicitacao" title="Abrir Solicitação" @ok="enviarSolicitacao">

        <div label-align-sm="left" label-cols-sm="4" label="Instituição" id="unidade" label-for="unidade">
            <select v-model="solicitacao.unidade" :options="unidades" size="sm"></select>
        </div>

        <template v-if="solicitacao.unidade == '' ">
            <!-- Registrar nova instituição -->

            <div label-align-sm="left" label-cols-sm="4" label="Nome" id="name" label-for="name">
                <input id="name" v-model="solicitacao.name" type="text"
                    placeholder="Nome da Unidade de Saúde"></input>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="CEP" id="cep" label-for="cep">
                <input id="cep" v-model="solicitacao.cep" type="number" min="0" max="99999999"
                    placeholder="99999999"></input>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="Endereço" id="endereco"
                label-for="endereco">
                <input id="endereco" v-model="solicitacao.address" type="text"
                    placeholder="Avenida do Endereço"></input>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="Número" id="numero" label-for="numero">
                <input id="numero" v-model="solicitacao.number" type="number" min="0" placeholder="999">
                </input>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="Bairro" id="bairro" label-for="bairro">
                <select v-model="solicitacao.neighborhood" :options="bairros" size="sm"></select>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="Cidade" id="cidade" label-for="cidade">
                <input id="cidade" v-model="solicitacao.city" type="text" placeholder="Cidade" disabled>
                </input>
            </div>

            <div label-align-sm="left" label-cols-sm="4" label="Estado" id="estado" label-for="estado">
                <input id="estado" v-model="solicitacao.state" max-length="2" type="text" placeholder="UF"
                    disabled></input>
            </div>

        </template>

        <label for="recursos">Recursos críticos</label>
        <select id="recursos" input-id="tags-basic" v-model="solicitacao.items" class="mb-2"></select>

    </div>

    <div id="modal-quero-ajuda" :title="'Quero Ajudar: ' + ajuda.titulo" @ok="enviarAjuda">

        <div label-align-sm="left" label-cols-sm="4" label="Nome da Empresa" id="instituicao"
            label-for="instituicao">
            <input id="instituicao" v-model="ajuda.empresa" placeholder="Instituição"></input>
        </div>

        <div label-align-sm="left" label-cols-sm="4" label="URL Site/Rede Social" id="site"
            label-for="site">
            <input id="site" v-model="ajuda.url" placeholder="Seu website"></input>
        </div>

        <div label-align-sm="left" label-cols-sm="4" label="URL Logotipo" id="logo"
            label-for="logo">
            <input id="logo" v-model="ajuda.logo" placeholder="Logotipo da empresa"></input>
        </div>

        <div label-align-sm="left" label-cols-sm="4" label="Email" id="email" label-for="email">
            <input id="email" v-model="ajuda.email"
                placeholder="Seu email para solicitante entrar em contato"></input>
        </div>

    </div>

    <div id="modal-token" :title="'Crie ou visualize os apoios recebidos'" @ok="enviarToken">

        <template>
            <div label-align-sm="left" label-cols-sm="4" label="Token" id="token" label-for="token">
                <input id="token" v-model="token" placeholder=""></input>
            </div>
        </template>           

    </div>

    <section class="mt-5 p-5 bg-roxo font-branca">
        <div class="container">
            <h3 class="text-center mb-5 font-weight-bolder">Empresas que apoiaram aparecem abaixo, <span>faça parte dessa rede do bem</span>!</h3>
            <div class="row mt-5">
                <template v-if="empresas.length > 0" v-for="item in empresas">
                    <div class="col-4 col-md-3 mb-5 text-center font-size-8">
                        <img v-if="item.logo" :src="item.logo" :alt="item.name" :title="item.name" class="img-fluid" style="max-width: 50px;" />
                        <template v-else>
                            @{{ item.name }}
                        </template>                     
                    </div>                            
                </template>
            </div>
        </div>        
    </section>

</section><!-- Lista de solicitação-->
@endsection