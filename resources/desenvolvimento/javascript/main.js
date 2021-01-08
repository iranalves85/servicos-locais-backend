import axios from "axios";
import Vue from "vue/dist/vue";
import moment from "moment";

//constantes
const api_url = 'http://localhost/desenvolvimento/servicos-locais/backend/app/public';
const api_servicodados_ibge = 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios/{municipio}/distritos'; 
const municipio = '3550308'; //São Paulo - SP 

//var axios = new Axios()  
var config = {
  el: '#app', 
  data: { 
    filtro: null,
    paged: null,
    bairros: [{
      value: '',
      text: 'Todos'
    }],
    solicitacao: {
      unidade: '',
      address: '',
      city: 'São Paulo',
      state: 'SP',
      items: []
    },
    ajuda: {
      titulo: '',
      empresa: '',
      logo: '',
      url: '',
      email: '',
      recurso_id: null,
      items: []
    },
    unidades: [{
      value: '',
      text: 'Adicionar novo'
    }],
    items: [],
    empresas: [],
    token: null,
    mostrarBotaoSolicitacao: false
  },
  methods: {
    //Carregar token quando inicializar
    carregarToken: function() {
      axios.get(api_url + '/token')
      .then((response) => {
        //Retorna se requisição for diferente
        if( response.status != 200) return false;

        //Se resposta for false
        if (typeof response.data == 'string' && response.data != 'false') {
          this.mostrarBotaoSolicitacao = true;
          this.token = response.data;
        }

      })
    },
    //Carregar lista de bairros para selects
    carregarBairros: function() {
      //Insere munícipio na url da requisição
      var url = api_servicodados_ibge.replace('{municipio}', municipio); 
      axios.get(url)
        .then((response) => {
          
          //Retorna se requisição for diferente
          if( response.status != 200) return;

          var bairros = response.data; //items
          for (const item of bairros) {
            this.bairros.push({
              value: item.nome,
              text: item.nome
            });
          }
        })
    },
    //Fazer filtragem por bairro
    filtrarBairro: function() {
      this.paged = null;
      this.items = [];
      this.carregarSolicitacao()
    },
    //carregar as últimas solicitações cadastradas
    carregarSolicitacao: function () {

      //Definir parametro de filtro
      var filtro = (this.filtro)? '/' + this.filtro : ''; 

      //Se houve parametro de paginação enviado
      if (this.paged == null || this.paged == 0) {
        //Definir parametro de filtro
        this.paged = '1'; 
      } 

      //Realiza requisição
      axios.get( api_url + '/request/'+ this.paged + filtro)
        .then((response) => {
          
          //Retorna se requisição for diferente
          if (response.status != 200) return;

          //Atribui items ao array
          response.data.forEach(element => {
            this.items.push(element); //items  
          });

          this.paged++; //incrementa paginação
          
        })
    },
    //carregar lista de unidades para selects
    carregarUnidades: function () {
      axios.get(api_url + '/unity')
        .then((response) => {

          //Retorna se requisição for diferente
          if (response.status != 200) return;

          for (const iterator of response.data) {
            this.unidades.push({
              value: iterator.ID,
              text: iterator.name
            })
          }
        })
    },
    carregarEmpresas: function () {
      axios.get(api_url + '/business')
      .then((response) => {

        //Retorna se requisição for diferente
        if (response.status != 200) return;

        this.empresas = response.data;

      })
    },
    //enviar solicitação para cadastro
    enviarSolicitacao: function (modalEvent) {
      modalEvent.preventDefault();
      
      $options = {
        method: 'POST',
        url: api_url + '/request',  
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': api_url
        },
        data: {
          solicitacao: this.solicitacao
        },
        withCredentials: true,
        crossorigin: true,
      };

      axios($options)
        .then((response) => {

          //Fechar modal
          this.$bvModal.hide('modal-solicitacao');

          //Se resposta foi ok
          if (response.status == 200) {
            
            $message = '';
            $title = '';

            //Sucesso
            if (response.data.success != undefined) {
              $title = 'Solicitação aberta!';
              $message = response.data.success.request;
              $variant = 'success';
              this.paged = '1';
              this.items = [];
              this.carregarSolicitacao();
            }

            //Erro
            if (response.data.error  != undefined) {
              $title = 'Erro na solicitação';
              $message = response.data.error.request;
              $variant = 'warning';
            }

            //Mostrar notificação
            this.$bvToast.toast($message, { 
              title: $title,
              autoHideDelay: 5000,
              appendToast: true,
              variant: $variant
            });

          }

        })
    },
    //enviar solicitação para cadastro
    editarSolicitacao: function (requestID, index, item) {
      
      //Fechar modal
      this.$bvModal.show('modal-solicitacao');

      for (const iterator of item.items) {
        this.solicitacao.items.push(iterator[1]); 
      }     
      
    },
    //enviar solicitação para cadastro
    deletarSolicitacao: function (requestID, index) {
      
      var dialog = confirm("Tem certeza que deseja excluir sua solicitação?");
      if (dialog == true) {
        
        $options = {
          method: 'DELETE',
          url: api_url + '/request/' + requestID,  
          headers: {
            'Content-Type': 'application/json',
            'Access-Control-Allow-Origin': api_url
          },
          withCredentials: true,
          crossorigin: true,
        };
  
        axios($options)
          .then((response) => {
  
            //Se resposta foi ok
            if (response.status == 200) {
              
              $message = '';
              $title = '';
  
              //Sucesso
              if (response.data.success != undefined) {
                $title = 'Solicitação aberta!';
                $message = response.data.success.request;
                $variant = 'success';
                this.items.splice(index, 1);
              }
  
              //Erro
              if (response.data.error  != undefined) {
                $title = 'Erro na solicitação';
                $message = response.data.error.request;
                $variant = 'warning';
              }
  
              //Mostrar notificação
              this.$bvToast.toast($message, { 
                title: $title,
                autoHideDelay: 5000,
                appendToast: true,
                variant: $variant
              });
  
            }
  
        })
      }
    },
    //enviar ajuda para cadastro
    enviarAjuda: function (modalEvent) {
      modalEvent.preventDefault();
      $options = {
        method: 'POST',
        url: api_url + '/resource',
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': api_url 
        },
        data: {
          ajuda: this.ajuda
        },
        withCredentials: true,
        crossorigin: true,
      };

      axios($options)
        .then((response) => {

          //Fechar modal
          this.$bvModal.hide('modal-quero-ajuda');

          //Se resposta foi ok
          if (response.status == 200) {

            $message = '';
            $title = '';

            //Sucesso
            if (response.data.success != undefined) {
              $title = 'Sua ajuda foi enviada!';
              $message = response.data.success.resource;
              $variant = 'success';
              this.paged = '1';
              this.items = [];
              this.carregarSolicitacao();
            }

            //Erro
            if (response.data.error != undefined) {
              $title = 'Erro no envio da ajuda.';
              $message = response.data.error.resource; 
              $variant = 'warning';
            }

            //Mostrar notificação
            this.$bvToast.toast($message, {
              title: $title,
              autoHideDelay: 5000,
              appendToast: true,
              variant: $variant
            });

          }

        })
    },
    //Registrar uma nova sessão
    registrarSessao: function () {
      axios.get(api_url + '/token/register')
      .then((response) => {
        //Se resposta foi ok
        if (response.status != 200)  return;

        var $message = '';
        var $title = '';

        //Sucesso
        if (response.data.success != undefined) {
          $title = 'A sessão foi iniciada!';
          $message = 'Sessão registrada, pode começar a solicitar!';
          $variant = 'success';
          this.token = response.data.success.token;
          this.filtrarBairro();
        }

        //Erro
        if (response.data.error != undefined) {
          $title = 'Erro no registro de sessão.';
          $message = response.data.error.token;
          $variant = 'warning';
        }

        //Mostrar notificação
        this.$bvToast.toast($message, {
          title: $title,
          autoHideDelay: 5000,
          appendToast: true,
          variant: $variant
        });        

      })
    },
    //Envio de token para registrar sessão
    enviarToken: function (modalEvent) {
      modalEvent.preventDefault();
      $options = {
        method: 'POST',
        url: api_url + '/token',  
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': api_url 
        },
        data: {
          token: this.token
        },
        withCredentials: true,
        crossorigin: true, 
      };

      axios($options)
        .then((response) => {

          //Fechar modal
          this.$bvModal.hide('modal-token');

          //Se resposta foi ok
          if (response.status == 200) {
            
            $message = '';
            $title = '';

            //Sucesso
            if (response.data.success != undefined) {
              $title = 'A sessão foi carregada!';
              $message = 'Sessão registrada, pode começar a solicitar!';
              $variant = 'success';
              this.token = response.data.success.token;
              this.filtrarBairro();
            }

            //Erro
            if (response.data.error != undefined) {
              $title = 'Erro no envio do token de sessão.';
              $message = response.data.error.token;
              $variant = 'warning';
            }

            //Mostrar notificação
            this.$bvToast.toast($message, {
              title: $title,
              autoHideDelay: 5000,
              appendToast: true,
              variant: $variant
            });

          }

        })
    },
    //Define configuração de botão ajuda
    setarAjuda: function ($tag) {
      this.ajuda.recurso_id   = $tag[0];
      this.ajuda.titulo       = $tag[1];
    },
    momentFrom: function ($date) {
      return moment($date).locale('pt-br').fromNow();
    }
  },
  mounted: function () {
    this.carregarToken()  
    this.carregarSolicitacao()
    this.carregarBairros()
    this.carregarUnidades()
    this.carregarEmpresas() 
  }
};

new Vue(config);