			<div class="block">
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					<h2><a href="<%= $this->url_for('index') %>"><%= ucfirst($this->_entities) %></a></h2>
					<ul>
						<li><a href="<%= $this->url_for('new') %>"><%= __('Add') %></a></li>
					</ul>
					<%
					echo $searchForm->open();
					echo $searchForm->search->def([
						'attrs'	=>	[
							'class'	=>	'text',
							'placeholder'	=>	'Search',
						]
					]);
					echo $searchForm->close();
					%>
				</div>	
				
				<div class="block_content">
				<!-- 	<div class="block small left" style="width:100%">
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							<h2>Liste</h2>	
						</div>	
						<div class="block_content"> -->
						
							<% $this->getFlash()->showAll() %>
						
							<% if(count($<?=$entity['meta']['plural'] ?>) == 0): %>
							<div style="text-align:center; font-weight:bold"><%= __('No element') %></div>
							<% else: %>
							<form action="" method="post">
								<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
								
									<thead>
										<tr>
											<th width="10"><input type="checkbox" class="check_all" /></th>
											<th><%= __('Created at') %></th>
											<th><%= __('Title') %></th>
											<td>&nbsp;</td>
										</tr>
									</thead>
									
									<tbody>
										<% foreach($<?=$entity['meta']['plural'] ?> as $<?=$entity['meta']['name'] ?>) { %>								
											<tr>
												<td><input type="checkbox" name="id[]" value="<%= $<?=$entity['meta']['name'] ?>->id %>" /></td>
												<td><%= $<?=$entity['meta']['name'] ?>->created_at %></td>
												<td><a href="<%= $this->url_for('edit', ['id'=>$<?=$entity['meta']['name'] ?>->id]) %>"><%= $<?=$entity['meta']['name'] ?> %></a></td>
												<td class="actions">
													<% $this->app['hooks']->trigger('asgard_actions', [$<?=$entity['meta']['name'] ?>]) %>
													<a class="delete" href="<%= $this->url_for('delete', ['id'=>$<?=$entity['meta']['name'] ?>->id]) %>"><%= __('Delete') %></a>
												</td>
											</tr>
										<% } %>
									</tbody>
									
								</table>
								<div class="tableactions">
									<select name="action">
										<option><%= __('Actions') %></option>
										<% foreach($globalactions as $name=>$action): %>
										<option value="<%= $name %>"><%= $action['text'] %></option>
										<% endforeach %>
									</select>
									<input type="submit" class="submit tiny" value="<%= __('Apply') %>" />
								</div>		
								
								<% if(isset($paginator) && $paginator->getPages()>1): %>
								<div class="pagination right">
									<%= $paginator->render() %>
								</div>
								<% endif %>
								
							</form>
							<% endif %>
						</div>		<!-- .block_content ends -->
						<!-- <div class="bendl"></div>
						<div class="bendr"></div>
					</div> -->
					<!--<div class="block small right" style="width:19%">
						<div class="block_head">
							<div class="bheadl"></div>
							<div class="bheadr"></div>
							
							<h2>Filtres</h2>
						</div>	
						<div class="block_content">
							<%
							%>
							
						</div>		
						
						<div class="bendl"></div>
						<div class="bendr"></div>
					</div>-->
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>		