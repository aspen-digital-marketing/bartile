"use strict";(self.webpackChunk_wcAdmin_webpackJsonp=self.webpackChunk_wcAdmin_webpackJsonp||[]).push([[4854],{43707:function(e,t,r){r.d(t,{Z:function(){return w}});var a=r(69307),o=r(65736),n=r(94333),s=r(69771),l=r(9818),i=r(92819),c=r(7862),m=r.n(c),d=r(86020),u=r(67221),p=r(81921),y=r(54071),_=r(5945),f=r(10431);function h(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(!e||0===e.length)return null;const a=e.slice(0),o=a.pop();if(o.showFilters(t,r)){const e=(0,f.flattenFilters)(o.filters),r=t[o.param]||o.defaultValue||"all";return(0,i.find)(e,{value:r})}return h(a,t,r)}function g(e){return t=>(0,s.format)(e,t)}class b extends a.Component{shouldComponentUpdate(e){return e.isRequesting!==this.props.isRequesting||e.primaryData.isRequesting!==this.props.primaryData.isRequesting||e.secondaryData.isRequesting!==this.props.secondaryData.isRequesting||!(0,i.isEqual)(e.query,this.props.query)}getItemChartData(){const{primaryData:e,selectedChart:t}=this.props;return e.data.intervals.map((function(e){const r={};return e.subtotals.segments.forEach((function(e){if(e.segment_label){const a=r[e.segment_label]?e.segment_label+" (#"+e.segment_id+")":e.segment_label;r[e.segment_id]={label:a,value:e.subtotals[t.key]||0}}})),{date:(0,s.format)("Y-m-d\\TH:i:s",e.date_start),...r}}))}getTimeChartData(){const{query:e,primaryData:t,secondaryData:r,selectedChart:a,defaultDateRange:o}=this.props,n=(0,p.getIntervalForQuery)(e,o),{primary:l,secondary:i}=(0,p.getCurrentDates)(e,o);return t.data.intervals.map((function(t,o){const c=(0,p.getPreviousDate)(t.date_start,l.after,i.after,e.compare,n),m=r.data.intervals[o];return{date:(0,s.format)("Y-m-d\\TH:i:s",t.date_start),primary:{label:`${l.label} (${l.range})`,labelDate:t.date_start,value:t.subtotals[a.key]||0},secondary:{label:`${i.label} (${i.range})`,labelDate:c.format("YYYY-MM-DD HH:mm:ss"),value:m&&m.subtotals[a.key]||0}}}))}getTimeChartTotals(){const{primaryData:e,secondaryData:t,selectedChart:r}=this.props;return{primary:(0,i.get)(e,["data","totals",r.key],null),secondary:(0,i.get)(t,["data","totals",r.key],null)}}renderChart(e,t,r,n){const{emptySearchResults:s,filterParam:l,interactiveLegend:i,itemsLabel:c,legendPosition:m,path:y,query:_,selectedChart:f,showHeaderControls:h,primaryData:b,defaultDateRange:w}=this.props,v=(0,p.getIntervalForQuery)(_,w),C=(0,p.getAllowedIntervalsForQuery)(_,w),R=(0,p.getDateFormatsForInterval)(v,b.data.intervals.length,{type:"php"}),S=s?(0,o.__)("No data for the current search","woocommerce"):(0,o.__)("No data for the selected date range","woocommerce"),{formatAmount:D,getCurrencyConfig:E}=this.context;return(0,a.createElement)(d.Chart,{allowedIntervals:C,data:r,dateParser:"%Y-%m-%dT%H:%M:%S",emptyMessage:S,filterParam:l,interactiveLegend:i,interval:v,isRequesting:t,itemsLabel:c,legendPosition:m,legendTotals:n,mode:e,path:y,query:_,screenReaderFormat:g(R.screenReaderFormat),showHeaderControls:h,title:f.label,tooltipLabelFormat:g(R.tooltipLabelFormat),tooltipTitle:"time-comparison"===e&&f.label||null,tooltipValueFormat:(0,u.getTooltipValueFormat)(f.type,D),chartType:(0,p.getChartTypeForQuery)(_),valueType:f.type,xFormat:g(R.xFormat),x2Format:g(R.x2Format),currency:E()})}renderItemComparison(){const{isRequesting:e,primaryData:t}=this.props;if(t.isError)return(0,a.createElement)(_.Z,null);const r=e||t.isRequesting,o=this.getItemChartData();return this.renderChart("item-comparison",r,o)}renderTimeComparison(){const{isRequesting:e,primaryData:t,secondaryData:r}=this.props;if(!t||t.isError||r.isError)return(0,a.createElement)(_.Z,null);const o=e||t.isRequesting||r.isRequesting,n=this.getTimeChartData(),s=this.getTimeChartTotals();return this.renderChart("time-comparison",o,n,s)}render(){const{mode:e}=this.props;return"item-comparison"===e?this.renderItemComparison():this.renderTimeComparison()}}b.contextType=y.$,b.propTypes={filters:m().array,isRequesting:m().bool,itemsLabel:m().string,limitProperties:m().array,mode:m().string,path:m().string.isRequired,primaryData:m().object,query:m().object.isRequired,secondaryData:m().object,selectedChart:m().shape({key:m().string.isRequired,label:m().string.isRequired,order:m().oneOf(["asc","desc"]),orderby:m().string,type:m().oneOf(["average","number","currency"]).isRequired}).isRequired},b.defaultProps={isRequesting:!1,primaryData:{data:{intervals:[]},isError:!1,isRequesting:!1},secondaryData:{data:{intervals:[]},isError:!1,isRequesting:!1}};var w=(0,n.compose)((0,l.withSelect)(((e,t)=>{const{charts:r,endpoint:a,filters:o,isRequesting:n,limitProperties:s,query:l,advancedFilters:c}=t,m=s||[a],d=h(o,l),p=(0,i.get)(d,["settings","param"]),y=t.mode||function(e,t){if(e&&t){const r=(0,i.get)(e,["settings","param"]);if(!r||Object.keys(t).includes(r))return(0,i.get)(e,["chartMode"])}return null}(d,l)||"time-comparison",{woocommerce_default_date_range:_}=e(u.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings"),f=e(u.REPORTS_STORE_NAME),g={mode:y,filterParam:p,defaultDateRange:_};if(n)return g;const b=m.some((e=>l[e]&&l[e].length));if(l.search&&!b)return{...g,emptySearchResults:!0};const w=r&&r.map((e=>e.key)),v=(0,u.getReportChartData)({endpoint:a,dataType:"primary",query:l,selector:f,limitBy:m,filters:o,advancedFilters:c,defaultDateRange:_,fields:w});if("item-comparison"===y)return{...g,primaryData:v};const C=(0,u.getReportChartData)({endpoint:a,dataType:"secondary",query:l,selector:f,limitBy:m,filters:o,advancedFilters:c,defaultDateRange:_,fields:w});return{...g,primaryData:v,secondaryData:C}})))(b)},50933:function(e,t,r){var a=r(69307),o=r(65736),n=r(94333),s=r(9818),l=r(7862),i=r.n(l),c=r(10431),m=r(86020),d=r(81595),u=r(67221),p=r(81921),y=r(14599),_=r(5945),f=r(54071);class h extends a.Component{formatVal(e,t){const{formatAmount:r,getCurrencyConfig:a}=this.context;return"currency"===t?r(e):(0,d.formatValue)(a(),t,e)}getValues(e,t){const{emptySearchResults:r,summaryData:a}=this.props,{totals:o}=a,n=o.primary?o.primary[e]:0,s=o.secondary?o.secondary[e]:0,l=r?0:n,i=r?0:s;return{delta:(0,d.calculateDelta)(l,i),prevValue:this.formatVal(i,t),value:this.formatVal(l,t)}}render(){const{charts:e,query:t,selectedChart:r,summaryData:n,endpoint:s,report:l,defaultDateRange:i}=this.props,{isError:d,isRequesting:u}=n;if(d)return(0,a.createElement)(_.Z,null);if(u)return(0,a.createElement)(m.SummaryListPlaceholder,{numberOfItems:e.length});const{compare:f}=(0,p.getDateParamsFromQuery)(t,i);return(0,a.createElement)(m.SummaryList,null,(t=>{let{onToggle:n}=t;return e.map((e=>{const{key:t,order:i,orderby:d,label:u,type:p,isReverseTrend:_,labelTooltipText:h}=e,g={chart:t};d&&(g.orderby=d),i&&(g.order=i);const b=(0,c.getNewPath)(g),w=r.key===t,{delta:v,prevValue:C,value:R}=this.getValues(t,p);return(0,a.createElement)(m.SummaryNumber,{key:t,delta:v,href:b,label:u,reverseTrend:_,prevLabel:"previous_period"===f?(0,o.__)("Previous period:","woocommerce"):(0,o.__)("Previous year:","woocommerce"),prevValue:C,selected:w,value:R,labelTooltipText:h,onLinkClickCallback:()=>{n&&n(),(0,y.recordEvent)("analytics_chart_tab_click",{report:l||s,key:t})}})}))}))}}h.propTypes={charts:i().array.isRequired,endpoint:i().string.isRequired,limitProperties:i().array,query:i().object.isRequired,selectedChart:i().shape({key:i().string.isRequired,label:i().string.isRequired,order:i().oneOf(["asc","desc"]),orderby:i().string,type:i().oneOf(["average","number","currency"]).isRequired}).isRequired,summaryData:i().object,report:i().string},h.defaultProps={summaryData:{totals:{primary:{},secondary:{}},isError:!1}},h.contextType=f.$,t.Z=(0,n.compose)((0,s.withSelect)(((e,t)=>{const{charts:r,endpoint:a,limitProperties:o,query:n,filters:s,advancedFilters:l}=t,i=o||[a],c=i.some((e=>n[e]&&n[e].length));if(n.search&&!c)return{emptySearchResults:!0};const m=r&&r.map((e=>e.key)),{woocommerce_default_date_range:d}=e(u.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings");return{summaryData:(0,u.getSummaryNumbers)({endpoint:a,query:n,select:e,limitBy:i,filters:s,advancedFilters:l,defaultDateRange:d,fields:m}),defaultDateRange:d}})))(h)},2765:function(e,t,r){r.d(t,{O3:function(){return s},be:function(){return i},u8:function(){return l}});var a=r(65736),o=r(92694),n=r(68734);const s=(0,o.applyFilters)("woocommerce_admin_downloads_report_charts",[{key:"download_count",label:(0,a.__)("Downloads","woocommerce"),type:"number"}]),l=(0,o.applyFilters)("woocommerce_admin_downloads_report_filters",[{label:(0,a.__)("Show","woocommerce"),staticParams:["chartType","paged","per_page"],param:"filter",showFilters:()=>!0,filters:[{label:(0,a.__)("All downloads","woocommerce"),value:"all"},{label:(0,a.__)("Advanced filters","woocommerce"),value:"advanced"}]}]),i=(0,o.applyFilters)("woocommerce_admin_downloads_report_advanced_filters",{title:(0,a._x)("Downloads match {{select /}} filters","A sentence describing filters for Downloads. See screen shot for context: https://cloudup.com/ccxhyH2mEDg","woocommerce"),filters:{product:{labels:{add:(0,a.__)("Product","woocommerce"),placeholder:(0,a.__)("Search","woocommerce"),remove:(0,a.__)("Remove product filter","woocommerce"),rule:(0,a.__)("Select a product filter match","woocommerce"),title:(0,a.__)("{{title}}Product{{/title}} {{rule /}} {{filter /}}","woocommerce"),filter:(0,a.__)("Select product","woocommerce")},rules:[{value:"includes",label:(0,a._x)("Includes","products","woocommerce")},{value:"excludes",label:(0,a._x)("Excludes","products","woocommerce")}],input:{component:"Search",type:"products",getLabels:n.oC}},customer:{labels:{add:(0,a.__)("Username","woocommerce"),placeholder:(0,a.__)("Search customer username","woocommerce"),remove:(0,a.__)("Remove customer username filter","woocommerce"),rule:(0,a.__)("Select a customer username filter match","woocommerce"),title:(0,a.__)("{{title}}Username{{/title}} {{rule /}} {{filter /}}","woocommerce"),filter:(0,a.__)("Select customer username","woocommerce")},rules:[{value:"includes",label:(0,a._x)("Includes","customer usernames","woocommerce")},{value:"excludes",label:(0,a._x)("Excludes","customer usernames","woocommerce")}],input:{component:"Search",type:"usernames",getLabels:n.jk}},order:{labels:{add:(0,a.__)("Order #","woocommerce"),placeholder:(0,a.__)("Search order number","woocommerce"),remove:(0,a.__)("Remove order number filter","woocommerce"),rule:(0,a.__)("Select a order number filter match","woocommerce"),title:(0,a.__)("{{title}}Order #{{/title}} {{rule /}} {{filter /}}","woocommerce"),filter:(0,a.__)("Select order number","woocommerce")},rules:[{value:"includes",label:(0,a._x)("Includes","order numbers","woocommerce")},{value:"excludes",label:(0,a._x)("Excludes","order numbers","woocommerce")}],input:{component:"Search",type:"orders",getLabels:async e=>{const t=e.split(",");return await t.map((e=>({id:e,label:"#"+e})))}}},ip_address:{labels:{add:(0,a.__)("IP Address","woocommerce"),placeholder:(0,a.__)("Search IP address","woocommerce"),remove:(0,a.__)("Remove IP address filter","woocommerce"),rule:(0,a.__)("Select an IP address filter match","woocommerce"),title:(0,a.__)("{{title}}IP Address{{/title}} {{rule /}} {{filter /}}","woocommerce"),filter:(0,a.__)("Select IP address","woocommerce")},rules:[{value:"includes",label:(0,a._x)("Includes","IP addresses","woocommerce")},{value:"excludes",label:(0,a._x)("Excludes","IP addresses","woocommerce")}],input:{component:"Search",type:"downloadIps",getLabels:async e=>{const t=e.split(",");return await t.map((e=>({id:e,label:e})))}}}}})},90525:function(e,t,r){r.r(t),r.d(t,{default:function(){return q}});var a=r(69307),o=r(7862),n=r.n(o),s=r(2765),l=r(65736),i=r(9818),c=r(92819),m=r(76292),d=r.n(m),u=r(86020),p=r(10431),y=r(81595),_=r(74617),f=r(67221),h=r(81921),g=r(39705),b=r(54071),w=r(79205);class v extends a.Component{constructor(){super(),this.getHeadersContent=this.getHeadersContent.bind(this),this.getRowsContent=this.getRowsContent.bind(this),this.getSummary=this.getSummary.bind(this)}getHeadersContent(){return[{label:(0,l.__)("Date","woocommerce"),key:"date",defaultSort:!0,required:!0,isLeftAligned:!0,isSortable:!0},{label:(0,l.__)("Product title","woocommerce"),key:"product",isSortable:!0,required:!0},{label:(0,l.__)("File name","woocommerce"),key:"file_name"},{label:(0,l.__)("Order #","woocommerce"),screenReaderLabel:(0,l.__)("Order Number","woocommerce"),key:"order_number"},{label:(0,l.__)("Username","woocommerce"),key:"user_id"},{label:(0,l.__)("IP","woocommerce"),key:"ip_address"}]}getRowsContent(e){const{query:t}=this.props,r=(0,p.getPersistedQuery)(t),o=(0,w.O3)("dateFormat",h.defaultTableDateFormat);return(0,c.map)(e,(e=>{const{_embedded:t,date:n,file_name:s,file_path:i,ip_address:c,order_id:m,order_number:d,product_id:y,username:f}=e,{code:h,name:g}=t.product[0];let b,w;if("woocommerce_rest_product_invalid_id"===h)b=(0,l.__)("(Deleted)","woocommerce"),w=(0,l.__)("(Deleted)","woocommerce");else{const e=(0,p.getNewPath)(r,"/analytics/products",{filter:"single_product",products:y});b=(0,a.createElement)(u.Link,{href:e,type:"wc-admin"},g),w=g}return[{display:(0,a.createElement)(u.Date,{date:n,visibleFormat:o}),value:n},{display:b,value:w},{display:(0,a.createElement)(u.Link,{href:i,type:"external"},s),value:s},{display:(0,a.createElement)(u.Link,{href:(0,_.getAdminLink)(`post.php?post=${m}&action=edit`),type:"wp-admin"},d),value:d},{display:f,value:f},{display:c,value:c}]}))}getSummary(e){const{download_count:t=0}=e,{query:r,defaultDateRange:a}=this.props,o=(0,h.getCurrentDates)(r,a),n=d()(o.primary.after),s=d()(o.primary.before).diff(n,"days")+1,i=this.context.getCurrencyConfig();return[{label:(0,l._n)("day","days",s,"woocommerce"),value:(0,y.formatValue)(i,"number",s)},{label:(0,l._n)("Download","Downloads",t,"woocommerce"),value:(0,y.formatValue)(i,"number",t)}]}render(){const{query:e,filters:t,advancedFilters:r}=this.props;return(0,a.createElement)(g.Z,{endpoint:"downloads",getHeadersContent:this.getHeadersContent,getRowsContent:this.getRowsContent,getSummary:this.getSummary,summaryFields:["download_count"],query:e,tableQuery:{_embed:!0},title:(0,l.__)("Downloads","woocommerce"),columnPrefsKey:"downloads_report_columns",filters:t,advancedFilters:r})}}v.contextType=b.$;var C=(0,i.withSelect)((e=>{const{woocommerce_default_date_range:t}=e(f.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings");return{defaultDateRange:t}}))(v),R=r(62409),S=r(43707),D=r(50933),E=r(27410);class q extends a.Component{render(){const{query:e,path:t}=this.props;return(0,a.createElement)(a.Fragment,null,(0,a.createElement)(E.Z,{query:e,path:t,filters:s.u8,advancedFilters:s.be,report:"downloads"}),(0,a.createElement)(D.Z,{charts:s.O3,endpoint:"downloads",query:e,selectedChart:(0,R.Z)(e.chart,s.O3),filters:s.u8,advancedFilters:s.be}),(0,a.createElement)(S.Z,{charts:s.O3,endpoint:"downloads",path:t,query:e,selectedChart:(0,R.Z)(e.chart,s.O3),filters:s.u8,advancedFilters:s.be}),(0,a.createElement)(C,{query:e,filters:s.u8,advancedFilters:s.be}))}}q.propTypes={query:n().object.isRequired}},69629:function(e,t,r){r.d(t,{I:function(){return o}});var a=r(65736);function o(e){return[e.country,e.state,e.name||(0,a.__)("TAX","woocommerce"),e.priority].map((e=>e.toString().toUpperCase().trim())).filter(Boolean).join("-")}},68734:function(e,t,r){r.d(t,{FI:function(){return f},V1:function(){return h},YC:function(){return u},hQ:function(){return p},jk:function(){return y},oC:function(){return _},qc:function(){return d},uC:function(){return g}});var a=r(96483),o=r(86989),n=r.n(o),s=r(92819),l=r(10431),i=r(67221),c=r(69629),m=r(79205);function d(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:s.identity;return function(){let r=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",o=arguments.length>1?arguments[1]:void 0;const s="function"==typeof e?e(o):e,i=(0,l.getIdsFromQuery)(r);if(i.length<1)return Promise.resolve([]);const c={include:i.join(","),per_page:i.length};return n()({path:(0,a.addQueryArgs)(s,c)}).then((e=>e.map(t)))}}d(i.NAMESPACE+"/products/attributes",(e=>({key:e.id,label:e.name})));const u=d(i.NAMESPACE+"/products/categories",(e=>({key:e.id,label:e.name}))),p=d(i.NAMESPACE+"/coupons",(e=>({key:e.id,label:e.code}))),y=d(i.NAMESPACE+"/customers",(e=>({key:e.id,label:e.name}))),_=d(i.NAMESPACE+"/products",(e=>({key:e.id,label:e.name}))),f=d(i.NAMESPACE+"/taxes",(e=>({key:e.id,label:(0,c.I)(e)})));function h(e){let{attributes:t,name:r}=e;const a=(0,m.O3)("variationTitleAttributesSeparator"," - ");if(r&&r.indexOf(a)>-1)return r;const o=(t||[]).map((e=>{let{option:t}=e;return t})).join(", ");return o?r+a+o:r}const g=d((e=>{let{products:t}=e;return t?i.NAMESPACE+`/products/${t}/variations`:i.NAMESPACE+"/variations"}),(e=>({key:e.id,label:h(e)})))},62409:function(e,t,r){r.d(t,{Z:function(){return o}});var a=r(92819);function o(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];const r=(0,a.find)(t,{key:e});return r||t[0]}}}]);